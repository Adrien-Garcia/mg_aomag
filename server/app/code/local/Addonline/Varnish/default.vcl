# This is a basic VCL configuration file for Addonline_Varnish magento module
#
# Default backend definition.  Set this to point to your magento server.
#
#backend default {
#    .host = "127.0.0.1";
#    .port = "8080";
#}
backend mag1 {
    .host = "bk_mag1";
    .port = "8080";
}

backend mag2 {
    .host = "bk_mag2";
    .port = "8080";
}
#
# Acl purge definition.  Set this to point to your magento back-office server.
#
acl purge {
     "localhost";
     "bk_mag1";
     "bk_mag2";
}

sub vcl_recv {

        # Purge
        ## 
        if (req.request == "PURGE") {
            if (!client.ip ~ purge) {
                error 405 "Not allowed.";
            }
            purge("req.url ~ " req.url);
            error 200 "Purged.";
        }

        # see http://www.varnish-cache.org/trac/wiki/VCLExampleNormalizeAcceptEncoding
        ### parse accept encoding rulesets to normalize
        if (req.http.Accept-Encoding) {
                if (req.url ~ "\.(jpg|jpeg|png|gif|gz|tgz|bz2|tbz|mp3|ogg|swf|mp4|flv)$") {
                        # don't try to compress already compressed files
                        remove req.http.Accept-Encoding;
                } elsif (req.http.Accept-Encoding ~ "gzip") {
                        set req.http.Accept-Encoding = "gzip";
                } elsif (req.http.Accept-Encoding ~ "deflate") {
                        set req.http.Accept-Encoding = "deflate";
                } else {
                        # unkown algorithm
                        remove req.http.Accept-Encoding;
                }
        }

       # on ajoute l'ip du client au header x-forwarded-for
       if (req.http.x-forwarded-for) {
         set req.http.X-Forwarded-For =
             req.http.X-Forwarded-For ", " client.ip;
       } else {
         set req.http.X-Forwarded-For = client.ip;
       }
       
       # on ne traite que les types de requête connues
       if (req.request != "GET" &&
         req.request != "HEAD" &&
         req.request != "PUT" &&
         req.request != "POST" &&
         req.request != "TRACE" &&
         req.request != "OPTIONS" &&
         req.request != "DELETE") {
           /* Non-RFC2616 or CONNECT which is weird. */
           return (pipe);
       }
       
        # Some known-static file types
        if (req.url ~ "^[^?]*\.(css|js|htc|xml|txt|swf|flv|pdf|gif|jpe?g|png|ico)$") {
                # Pretent no cookie was passed
                unset req.http.Cookie;
        }

        # Force lookup if the request is a no-cache request from the client.
        if (req.http.Cache-Control ~ "no-cache") {
                purge_url(req.url);
        }

       
       /* We only deal with GET and HEAD by default */
       if (req.request != "GET" && req.request != "HEAD") {
           return (pass);
       }
       
       # if (req.http.Authorization || req.http.Cookie) {
       # le fontionnement de magento nécéssite des cookies on va donc chercher en cache quand même les requêtes avec des cookies
       if (req.http.Authorization) {
             /* Not cacheable by default */
           return (pass);
       }
       
      return (lookup);
}

/*
Remove cookies from backend response so this page can be cached
*/
sub vcl_fetch {
        #set req.backend = default;
        
        if (req.http.Host == "aomagento.addonline.biz") {
                set req.backend = mag1;
        }
        if (req.http.Host == "www.covalab.com") {
                set req.backend = mag2;
        }
        
        if (beresp.status == 302 || beresp.status == 301 || beresp.status == 418) {
                return (pass);
        }
        if (beresp.http.varnish == "cache") {
                remove beresp.http.Set-Cookie;
                remove beresp.http.X-Cache;
                remove beresp.http.Server;
                remove beresp.http.Age;
                remove beresp.http.Pragma;
                set beresp.http.Cache-Control = "public";
                set beresp.grace = 2m;
                set beresp.http.X_VARNISH_FETCH = "Removed cookie in vcl_fetch";
        } else {
                set beresp.http.X_VARNISH_FETCH = "Nothing removed";
        }

        # Some known-static file types
        if (req.url ~ "^[^?]*\.(css|js|htc|xml|txt|swf|flv|pdf|gif|jpe?g|png|ico)$") {
                # Force caching
                remove beresp.http.Pragma;
                remove beresp.http.Set-Cookie;
                set beresp.http.Cache-Control = "public";
        }

        if (!beresp.cacheable) {
                return (pass);
        }
        return (deliver);
}

/*
Adding debugging information
*/
sub vcl_deliver {
        if (obj.hits > 0) {
                set resp.http.X-Cache = "HIT";
                set resp.http.Server = "Varnish (HIT)";
        } else {
                set resp.http.X-Cache = "MISS";
                set resp.http.Server = "Varnish (MISS)";
        }
}

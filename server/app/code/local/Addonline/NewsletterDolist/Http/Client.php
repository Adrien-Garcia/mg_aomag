<?php
class Addonline_NewsletterDolist_Http_Client extends Zend_Http_Client
{
	
	protected function _prepareBody()
    {
    	    	
        // According to RFC2616, a TRACE request should not have a body.
        if ($this->method == self::TRACE) {
            return '';
        }

        if (isset($this->raw_post_data) && is_resource($this->raw_post_data)) {
            return $this->raw_post_data;
        }
        // If mbstring overloads substr and strlen functions, we have to
        // override it's internal encoding
        if (function_exists('mb_internal_encoding') &&
           ((int) ini_get('mbstring.func_overload')) & 2) {

            $mbIntEnc = mb_internal_encoding();
            mb_internal_encoding('ASCII');
        }

        // If we have raw_post_data set, just use it as the body.
        if (isset($this->raw_post_data)) {
            $this->setHeaders(self::CONTENT_LENGTH, strlen($this->raw_post_data));
            if (isset($mbIntEnc)) {
                mb_internal_encoding($mbIntEnc);
            }

            return $this->raw_post_data;
        }

        $body = '';

        // If we have files to upload, force enctype to multipart/form-data
        if (count ($this->files) > 0) {
            $this->setEncType(self::ENC_FORMDATA);
        }

        // If we have POST parameters or files, encode and add them to the body
        if (count($this->paramsPost) > 0 || count($this->files) > 0) {
            switch($this->enctype) {
                case self::ENC_FORMDATA:
                    // Encode body as multipart/form-data
                    $boundary = '---ZENDHTTPCLIENT-' . md5(microtime());
                    $this->setHeaders(self::CONTENT_TYPE, self::ENC_FORMDATA . "; boundary={$boundary}");

                    // Get POST parameters and encode them
                    $params = self::_flattenParametersArray($this->paramsPost);
                    foreach ($params as $pp) {
                        $body .= self::encodeFormData($boundary, $pp[0], $pp[1]);
                    }

                    // Encode files
                    foreach ($this->files as $file) {
                        $fhead = array(self::CONTENT_TYPE => $file['ctype']);
                        $body .= self::encodeFormData($boundary, $file['formname'], $file['data'], $file['filename'], $fhead);
                    }

                    $body .= "--{$boundary}--\r\n";
                    break;

                case self::ENC_URLENCODED:
                    // Encode body as application/x-www-form-urlencoded
                    $this->setHeaders(self::CONTENT_TYPE, self::ENC_URLENCODED);
                    //$body = http_build_query($this->paramsPost, '', '&');
                    
                    $body = '';
                    foreach ($this->paramsPost AS $key=>$value) {
						if (is_array($value)) {
							foreach ($value AS $val) {
								$body .= $key.'='.$val.'&';				
							}			
						} else {
                    		$body .= $key.'='.$value.'&';
						}
                    }
                    $body = rtrim($body, '&');
                    
                    break;

                default:
                    if (isset($mbIntEnc)) {
                        mb_internal_encoding($mbIntEnc);
                    }

                    /** @see Zend_Http_Client_Exception */
                    #require_once 'Zend/Http/Client/Exception.php';
                    throw new Zend_Http_Client_Exception("Cannot handle content type '{$this->enctype}' automatically." .
                        " Please use Zend_Http_Client::setRawData to send this kind of content.");
                    break;
            }
        }

        // Set the Content-Length if we have a body or if request is POST/PUT
        if ($body || $this->method == self::POST || $this->method == self::PUT) {
            $this->setHeaders(self::CONTENT_LENGTH, strlen($body));
        }

        if (isset($mbIntEnc)) {
            mb_internal_encoding($mbIntEnc);
        }

        return $body;
    }
}
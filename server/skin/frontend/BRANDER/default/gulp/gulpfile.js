var gulp = require('gulp');
var gulpif = require('gulp-if');
var less = require('gulp-less');
var path = require('path');
var autoprefixer = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var spritesmith = require('gulp.spritesmith');
var imagemin = require('gulp-imagemin');
var browserSync = require('browser-sync');
var reload      = browserSync.reload;
var sourcemaps = require('gulp-sourcemaps');
var minimist = require('minimist');
var plumber = require('gulp-plumber');
var	libPath = '../',
	knownOptions = {
	  string: 'env',
	  boolean: 'prod'
	};
var options = minimist(process.argv.slice(2), knownOptions);

var env = (options.env != undefined && options.env != "") ? options.env : false;
console.log('prod ? '+options.prod);
options.prod = (options.prod != undefined && options.prod) ? options.prod : false; 

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    console.log(out);
}

gulp.task('sprite', function() {
    
	/* SPRITE task */
	var spriteData = gulp.src(libPath+'images/client/origin/*.{png,jpg,gif}')
		.pipe(plumber({
		      	errorHandler: function (error) {
			        console.log(error.message);
			        this.emit('end');
			    }
			}))
		.pipe(spritesmith({
			imgName: 'spritesheet.png',
			imgPath: libPath+'images/client/sprites/spritesheet.png',
			cssName: 'origin.less'
		}));
	
	  	spriteData.img
	  		.pipe(imagemin())
	  		.pipe(gulp.dest(libPath+'images/client/sprites/'));
	  				
	  	spriteData.css
	  		.pipe(gulp.dest(libPath+'less/'));

	return spriteData;
});

var lessF;

if(options.prod) {
	lessF = function () {
	  return gulp.src(libPath+'less/styles.less')
	    	.pipe(less({}))
	    	.pipe(minifycss({}))
	    .pipe(gulp.dest(libPath+'css/'));
	};
} else {
	lessF = function () {
	  return gulp.src(libPath+'less/styles.less')
	  	.pipe(sourcemaps.init())
	    	.pipe(less({}))
		.pipe(sourcemaps.write())
	    .pipe(gulp.dest(libPath+'css/'));
	};
}
gulp.task('less', ['sprite'], lessF);




gulp.task('browser-sync', function() {

	browserSync({
        proxy: options.env,
        browser: ["default"],
       	host: options.env,
        open: false,
        port: 3000,
        logLevel: "debug"
    });

});

gulp.task('watch', function() {
	 
	/* WATCH task */
	if(env){
	 	gulp.watch(libPath+'images/client/origin/*.{png,jpg,gif}', ['sprite', 'less']).on('change', browserSync.reload);
	 	gulp.watch(libPath+'scss/*.scss', ['less']);
	 	gulp.watch(libPath+'css/*.css').on('change', reload({stream: true}));
	 	gulp.watch(libPath+'js/*.js', [ browserSync.reload]);
		gulp.watch(tplPath+'**/*.php').on('change', browserSync.reload);
	}
	else
	{
		gulp.watch(libPath+'images/client/origin/*.{png,jpg,gif}', ['sprite', 'less']);
	 	gulp.watch(libPath+'scss/*.scss', ['less']);
	 	if(options.prod)
	 		gulp.watch(libPath+'js/*.js', ['uglify']);
	}

});

if(env && !options.prod) // Browser sync
	gulp.task('default', [ 'sprite', 'browser-sync', 'watch'], function() {});
else if(options.prod) // Build Prod
	gulp.task('default', [ 'sprite', 'less'], function() {} );
else // Watch classic
	gulp.task('default', [ 'sprite', 'watch'], function() {});

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
var eslint = require('gulp-eslint');

/*******************************************************************************************************
   ____ ___  _   _ ____ _____
  / ___/ _ \| \ | / ___|_   _|
 | |  | | | |  \| \___ \ | |
 | |__| |_| | |\  |___) || |
  \____\___/|_| \_|____/ |_|

 *******************************************************************************************************/

var runTimestamp = Math.round(Date.now()/1000)
var	libPath = '../',

knownOptions = {
	string: 'env',
	string: 'nav'
};
options = minimist(process.argv.slice(2), knownOptions);

/*******************************************************************************************************
  ____  _______     __
 |  _ \| ____\ \   / /
 | | | |  _|  \ \ / /
 | |_| | |___  \ V /
 |____/|_____|  \_/

 *******************************************************************************************************/

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

var lessF = function () {
	  return gulp.src(libPath+'less/styles.less')
	  	.pipe(sourcemaps.init())
	    	.pipe(less({}))
		.pipe(sourcemaps.write())
	    .pipe(gulp.dest(libPath+'css/'));
	};
gulp.task('less', ['sprite'], lessF);



gulp.task('browser-sync', function() {

	browserSync({
        proxy: options.env, 
		   host: options.env,
        open: "external",
        browser: options.nav,
    });

});

gulp.task('watch', function() {
	 
	/* WATCH task */
	 	gulp.watch(libPath+'images/client/origin/*.{png,jpg,gif}', ['sprite', 'less']).on('change', browserSync.reload);
	 	gulp.watch(libPath+'scss/*.scss', ['less']);
	 	gulp.watch(libPath+'css/*.css').on('change', reload({stream: true}));
	 	gulp.watch(libPath+'js/*.js', [ browserSync.reload]);
		gulp.watch(tplPath+'**/*.php').on('change', browserSync.reload);

});

gulp.task('default', [ 'sprite', 'browser-sync', 'watch'], function() {});

/*******************************************************************************************************
  _     ___ _   _ _____
 | |   |_ _| \ | |_   _|
 | |    | ||  \| | | |
 | |___ | || |\  | | |
 |_____|___|_| \_| |_|

 *******************************************************************************************************/

gulp.task('js-lint', function() {
    // Be sure to return the stream from the task;
    // Otherwise, the task may end before the stream has finished.
    return gulp.src([jsPathApp, jsPathModules])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failAfterError());
});

gulp.task('scss-lint', function() {
    // Exclude templates/* files, they're not valid scss files
    return gulp.src([scssPath, '!'+libPath+'/scss/templates/*'])
        .pipe(sassLint({
            configFile: libPath+'/scss/.sass-lint.yml'
        }))
        .pipe(sassLint.format())
        .pipe(sassLint.failOnError())

});

gulp.task('lint', ['js-lint', 'scss-lint'], function() {});

/*******************************************************************************************************
  _____ _____ ____ _____
 |_   _| ____/ ___|_   _|
   | | |  _| \___ \ | |
   | | | |___ ___) || |
   |_| |_____|____/ |_|

 *******************************************************************************************************/
gulp.task('test', [], function() {});

/*******************************************************************************************************
  ____  _   _ ___ _     ____
 | __ )| | | |_ _| |   |  _ \
 |  _ \| | | || || |   | | | |
 | |_) | |_| || || |___| |_| |
 |____/ \___/|___|_____|____/

 *******************************************************************************************************/

gulp.task('sass-build', function() {
	gulp.src(scssPath)
        .pipe(plumber())
        .pipe(sass({ style: 'compressed' }))
        .pipe(autoprefixer('last 2 version'))
        .pipe(plumber.stop())
        .pipe(minifycss())
        .pipe(gulp.dest(cssPath))
});

gulp.task('js-build', function() {
    return gulp.src([jsPathApp, jsPathModules])
        .pipe(concat('all.js'))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(jsMinPath));
});

gulp.task('build', ['sprite', 'iconfont', 'sass-build', 'js-build'], function() {});

var gulp = require('gulp'),
sass = require('gulp-sass'),
autoprefixer = require('gulp-autoprefixer'),
minifycss = require('gulp-minify-css'),
rename = require('gulp-rename'),
spritesmith = require('gulp.spritesmith'),
imagemin = require('gulp-imagemin'),
browserSync = require('browser-sync'),
reload      = browserSync.reload,
uglify = require('gulp-uglify'),
sourcemaps = require('gulp-sourcemaps'),
minimist = require('minimist'),
plumber = require('gulp-plumber'),
concat = require('gulp-concat'),
iconfont = require('gulp-iconfont'),
consolidate = require('gulp-consolidate'),
eslint = require('gulp-eslint'),
sassLint = require('gulp-sass-lint'),
codepoints = require('code-points'); //codepoints utilisé par iconfont

/*******************************************************************************************************
  ____ ___  _   _ ____ _____
 / ___/ _ \| \ | / ___|_   _|
| |  | | | |  \| \___ \ | |
| |__| |_| | |\  |___) || |
 \____\___/|_| \_|____/ |_|

*******************************************************************************************************/


runTimestamp = Math.round(Date.now()/1000),
libPath = '../';

scssPath = libPath+'scss/**/*.scss',
cssPath = libPath+'css',
imagesPath = libPath+'images/client/origin/*.{png,jpg,gif}',
iconsPath = libPath+'images/client/svgicons/*.svg',
jsPathApp = libPath+'js/app.js',
jsPathModules = libPath+'js/application/*.js',
jsMinPath = libPath+'/js',
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
function handleError (err) {
    console.log(err.toString())
    this.emit('end')
}


gulp.task('sass-dev', function() {

    /* SASS task */
    return gulp.src(scssPath)
    .pipe(plumber())
	.pipe(sass({
		includePaths: [libPath+'/scss/'],
        style: 'compressed',
		errLogToConsole: true
	}))
    .on('error', handleError)
    .pipe(sourcemaps.init())
    .pipe(autoprefixer('last 2 version'))
    .pipe(gulp.dest(cssPath))
    .pipe(sourcemaps.write())
    .pipe(plumber.stop())
    .pipe(gulp.dest(cssPath))
    .pipe(reload({stream: true}));

});

gulp.task('js-dev', function() {
  return gulp.src([jsPathApp, jsPathModules])
        .pipe(sourcemaps.init())
        .pipe(concat('all.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(jsMinPath));
});


gulp.task('sprite', function() {

    /* SPRITE task */
	var spriteData = gulp.src(imagesPath)
    .pipe(plumber({
        errorHandler: function (error) {
            console.log(error.message);
            this.emit('end');
        }
    }))
    .pipe(spritesmith({
        imgName: 'spritesheet.png',
        imgPath: '../images/client/sprites/spritesheet.png',
        cssTemplate : libPath+'scss/templates/spritesheet.scss.handlebars',
        cssName: '_spritesheet.scss',
    }));

    spriteData.img
    .pipe(imagemin())
    .pipe(gulp.dest(libPath+'images/client/sprites/'));

    return spriteData.css
    .pipe(gulp.dest(libPath+'scss/mixin/'));

});

gulp.task('iconfont', function () {

	/* SPRITE task */
  	return gulp.src([iconsPath])
        .pipe(iconfont({
            fontName: 'computec-font',
            normalize: true,
            fontHeight: 1001,
            appendUnicode: false,
            formats: ['ttf', 'eot', 'woff', 'woff2', 'svg'],
            timestamp: runTimestamp
    }))
    .on('glyphs', function (glyphs, options) {
        gulp.src(libPath+'scss/templates/_icons.scss')
                    .pipe(consolidate('lodash', {
                        glyphs: glyphs,
                        fontName: 'computec-font',
                        fontPath: 'fonts/svgfont/',
                        className: 'icon'
                    }))
        .pipe(gulp.dest(libPath+'scss/module/'));
    })
    .pipe(gulp.dest(libPath+'fonts/svgfont'));
});

gulp.task('browser-sync', function() {

    return browserSync({
        proxy: options.env,
        host: options.env,
        open: "external",
        browser: options.nav,
});

});

gulp.task('watch', function() {

    /* WATCH task */
    gulp.watch(imagesPath, ['sprite']).on('change', browserSync.reload);
    gulp.watch(iconsPath, ['iconfont']).on('change', browserSync.reload);
    gulp.watch(scssPath, ['sass-dev']);
    gulp.watch([jsPathApp, jsPathModules], ['js-dev', browserSync.reload]);
    gulp.watch('../../../../app/design/frontend/COMPUTEC/default/template/**/*.phtml').on('change', browserSync.reload);

});

gulp.task('default', [ 'sprite', 'iconfont', 'sass-dev', 'js-dev', 'browser-sync', 'watch'], function() {});

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

gulp.task('sass-build', ['sprite', 'iconfont'], function() {
	return gulp.src(scssPath)
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

gulp.task('build', ['sass-build', 'js-build'], function() {});

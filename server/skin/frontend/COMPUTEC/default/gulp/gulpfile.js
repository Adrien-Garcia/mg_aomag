var gulp = require('gulp'),
gulpif = require('gulp-if'),
sass = require('gulp-sass'),
autoprefixer = require('gulp-autoprefixer'),
minifycss = require('gulp-minify-css'),
rename = require('gulp-rename'),
spritesmith = require('gulp.spritesmith'),
imagemin = require('gulp-imagemin'),
browserSync = require('browser-sync'),
reload      = browserSync.reload,
uglify = require('gulp-uglify'),
gutil = require('gulp-util'),
sourcemaps = require('gulp-sourcemaps'),
minimist = require('minimist'),
plumber = require('gulp-plumber'),
iconfont = require('gulp-iconfont'),
consolidate = require('gulp-consolidate'),
async = require('async'),
codepoints = require('code-points'),
concat = require('gulp-concat'),
eslint = require('gulp-eslint'),
runTimestamp = Math.round(Date.now()/1000),
libPath = '../';

console.log(libPath+'fonts/svgfont');

var knownOptions = {
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

gulp.task('iconfont', function(done){
  var iconStream = gulp.src([libPath+'images/client/svgicons/*.svg'])
    .pipe(iconfont({
        fontName: 'computec-font',
        normalize: true,
        fontHeight: 1001,
        appendUnicode: false,
        formats: ['ttf', 'eot', 'woff', 'svg'],
        timestamp: runTimestamp
    }));

  async.parallel([
    function handleGlyphs (cb) {
      iconStream.on('glyphs', function(glyphs, options) {
        gulp.src(libPath+'scss/templates/_icons.scss')
          .pipe(consolidate('lodash', {
            glyphs: glyphs,
            fontName: 'computec-font',
            fontPath: 'fonts/svgfont/',
            className: 'icon'
          }))
          .pipe(gulp.dest(libPath+'scss/module'))
          .on('finish', cb);
      });
    },
    function handleFonts (cb) {
      iconStream
        .pipe(gulp.dest(libPath+'fonts/svgfont'))
        .on('finish', cb);
    }
  ], done);
});

gulp.task('sass', function() {

    /* SASS task */
    gulp.src(libPath+'scss/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(plumber())
    .pipe(sass({ style: 'compressed' }))
    .pipe(autoprefixer('last 2 version'))
    .pipe(plumber.stop())
    .pipe(gulp.dest(libPath+'css'))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(libPath+'css'))
    .pipe(reload({stream: true}))

});

// PROD VERSION
gulp.task('sass-prod', function() {

    gulp.src(libPath+'scss/**/*.scss')

    .pipe(plumber())
    .pipe(sass({ style: 'compressed' }))
    .pipe(autoprefixer('last 2 version'))
    .pipe(plumber.stop())
    .pipe(gulp.dest(libPath+'css'))

});

/*gulp.task('uglify', function() {

    gulp.src([libPath+'js/app.js','!'+libPath+'js/app.min.js'])
    .pipe(uglify())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(libPath+'js/'))

});*/

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
        imgPath: '../images/client/sprites/spritesheet.png',
        cssTemplate : libPath+'scss/templates/spritesheet.scss.handlebars',
        cssName: '_spritesheet.scss',
        algorithm: 'top-down'
    }));

    spriteData.img
    .pipe(imagemin())
    .pipe(gulp.dest(libPath+'images/client/sprites/'));

    spriteData.css
    .pipe(gulp.dest(libPath+'scss/mixin/'));

});

gulp.task('browser-sync', function() {

    browserSync({
        proxy: options.env,
        browser: [],
        host: options.env,
        open: true,
        port: 3003
        //logLevel: "debug"
});

});

gulp.task('watch', function() {

    /* WATCH task */
    if(env){
        gulp.watch(libPath+'images/client/origin/*.{png,jpg,gif}', ['sprite']);
        gulp.watch(libPath+'images/client/svgicons/*.*', ['iconfont']);
        console.log(libPath+'images/client/svgicons/');
        gulp.watch(libPath+'scss/**/*.scss', ['sass']);
        gulp.watch(libPath+'js/**/*.js', ['concat-js']).on('change', browserSync.reload);
    }
    else
    {
        gulp.watch(libPath+'images/client/origin/*.{png,jpg,gif}', ['sprite', 'sass-prod']);
        gulp.watch(libPath+'scss/**/*.scss', ['sass-prod']);
        if(options.prod)
            gulp.watch(libPath+'js/*.js', ['uglify']);
    }

});

gulp.task('concat-js', function() {
  return gulp.src([libPath+'js/app.js', libPath+'js/application/*.js'])
    .pipe(concat('all.js'))
    .pipe(gulp.dest(libPath+'js'))
    .pipe(eslint())
    // eslint.format() outputs the lint results to the console.
    // Alternatively use eslint.formatEach() (see Docs).
    .pipe(eslint.format())
    // To have the process exit with an error code (1) on
    // lint error, return the stream and pipe to failAfterError last.
   .pipe(eslint.failAfterError());
});

gulp.task('eslint', () => {
    // ESLint ignores files with "node_modules" paths.
    // So, it's best to have gulp ignore the directory as well.
    // Also, Be sure to return the stream from the task;
    // Otherwise, the task may end before the stream has finished.
    return gulp.src([libPath+'js/app.js', libPath+'js/application/*.js'])
    // eslint() attaches the lint output to the "eslint" property
    // of the file object so it can be used by other modules.
        .pipe(eslint())
        // eslint.format() outputs the lint results to the console.
        // Alternatively use eslint.formatEach() (see Docs).
        .pipe(eslint.format())
        // To have the process exit with an error code (1) on
        // lint error, return the stream and pipe to failAfterError last.
        .pipe(eslint.failAfterError());
});

if(env && !options.prod) // Browser sync
    gulp.task('default', [ 'concat-js', 'sprite', 'browser-sync', 'iconfont', 'watch'], function() {});
else if(options.prod) // Build Prod
    gulp.task('default', [ 'sprite', 'sass-prod'], function() {});
else // Watch classic
    gulp.task('default', [ 'sprite', 'browser-sync', 'watch'], function() {});
gulp.task('prod', [ 'sprite', 'sass-prod', 'concat-js', 'iconfont'], function() {});

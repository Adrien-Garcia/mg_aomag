module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    
    autoprefixer: {

        options: {
          map: true,
          browsers: ['last 2 versions']
        },

        // prefix the specified file
        single_file: {
          options: {
            // Target-specific options go here.
          },
          src: '../css/styles.css',
          dest: '../css/styles.css'
        }
    },
    
    less: {
        development: {
          options: {
            cleancss: false,
            strictImports : true,
            compress:true,
            sourceMap:true,
            sourceMapFilename: "../css/styles.css.map"
          },
          files: {
            "../css/styles.css": "../less/styles.less"
          }
        },
        production: {
          options: {
            cleancss: true
          },
          files: {
          "../css/styles.css": "../less/styles.less"
          }
        }
      },

      // Project configuration.
        uglify: {
          production: {
            files: {
              '../js/charte.min.js': ['../js/charte.js']
            }
          }
        },

  sprite: {
      all: {
        src: '../images/client/origin/*.png',
        dest: '../images/client/sprites/origin.png',
        destCss: '../less/origin.less',
        engine: 'pngsmith'
      }
    },
  
  watch: {
    css: {
      files: ['../less/**/*.less'],
      tasks: ['less:development', 'autoprefixer', 'bell'],
      options: {
        
      },
    },
    spriter: {
      files: ['../images/client/origin/**/*.png'],
      tasks: ['sprite:all'],
    }
  },
  browserSync: {
    dev: {
        bsFiles: {
            src : '../css/styles.css'
        },
        options: {
            proxy: "aomagento.delphine.jetpulp.dev",
            watchTask: true,
            logLevel: "debug",
            reloadOnRestart:false,
            open: "external",
            host: "aomagento.delphine.jetpulp.dev"
        }
    }
  }
  

  
  });
  grunt.loadNpmTasks('grunt-browser-sync');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-spritesmith');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-bell');
  grunt.loadNpmTasks('grunt-contrib-uglify');

 // Default task(s).
  grunt.registerTask('default', ['browserSync','sprite:all', 'less:development', 'uglify:production', 'watch']);
  grunt.registerTask('build', ['sprite:all', 'less:production','uglify:production', 'bell']);


};
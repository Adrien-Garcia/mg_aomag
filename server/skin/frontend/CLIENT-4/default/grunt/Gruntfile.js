module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    
    autoprefixer: {

        options: {
          // Task-specific options go here.
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
	    	cleancss: true
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
	}
	
  });
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-spritesmith');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-bell');

  // Default task(s).
  grunt.registerTask('default', ['sprite:all', 'less:development', 'autoprefixer', 'watch']);
  grunt.registerTask('build', ['sprite:all', 'less:production', 'bell']);

};
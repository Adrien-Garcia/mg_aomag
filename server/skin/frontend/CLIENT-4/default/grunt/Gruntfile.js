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
  
	watch: {
	  css: {
	    files: ['../less/**/*.less'],
	    tasks: ['less:development', 'autoprefixer', 'bell'],
	    options: {
	      
	    },
	  },
	  sprite: {
		  files: ['../images/client/origin/**/*.*'],
		  tasks: ['shell:glue'],
	  }
	},
	
	shell: {
        glue: {
            command: 'glue ../images/client/origin --less=../less --img=../images/client/sprites --sprite-namespace= --namespace= --cachebuster'
        }
    },
  });
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-shell');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-bell');

  // Default task(s).
  grunt.registerTask('default', ['shell:glue', 'less:development', 'autoprefixer', 'watch'; 'bell']);
  grunt.registerTask('build', ['shell:glue', 'less:production', 'bell']);

};
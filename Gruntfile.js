module.exports = function(grunt) {
	'use strict';

	var sass = require( 'node-sass' );

	require('load-grunt-tasks')(grunt);

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Update developer dependencies
		devUpdate: {
			packages: {
				options: {
					packageJson: null,
					packages: {
						devDependencies: true,
						dependencies: false
					},
					reportOnlyPkgs: [],
					reportUpdated: false,
					semver: true,
					updateType: 'force'
				}
			}
		},

		// SASS to CSS
		sass: {
			options: {
				implementation: sass,
				sourcemap: 'none'
			},
			dist: {
				files: {
					'assets/css/modal.css' : 'assets/scss/modal.scss'
				}
			}
		},

		// Post CSS
		postcss: {
			options: {
				//map: false,
				processors: [
					require('autoprefixer')({
						overrideBrowserslist: [
							'> 0.1%',
							'ie 8',
							'ie 9'
						]
					})
				]
			},
			dist: {
				src: [
					'!assets/css/*.min.css',
					'assets/css/*.css'
				]
			}
		},

		// Minify CSS
		cssmin: {
			options: {
				processImport: false,
				roundingPrecision: -1,
				shorthandCompacting: false
			},
			target: {
				files: [{
					expand: true,
					cwd: 'assets/css',
					src: [
						'*.css',
						'!*.min.css'
					],
					dest: 'assets/css',
					ext: '.min.css'
				}]
			}
		},

		// Minify JavaScript.
		uglify: {
			options: {
				compress: {
					global_defs: {
						"EO_SCRIPT_DEBUG": false
					},
					dead_code: true
				},
				banner: '/*! <%= pkg.title %> v<%= pkg.version %> <%= grunt.template.today("dddd dS mmmm yyyy HH:MM:ss TT Z") %> */'
			},
			build: {
				files: [{
					expand: true, // Enable dynamic expansion.
					src: [
						'assets/js/*.js',
						'!assets/js/*.min.js'
					],
					ext: '.min.js', // Dest filepaths will have this extension.
				}]
			}
		},

		// Check for Javascript errors.
		jshint: {
			options: {
				reporter: require('jshint-stylish'),
				globals: {
					"EO_SCRIPT_DEBUG": false,
				},
				'-W099': true, // Mixed spaces and tabs
				'-W083': true, // Fix functions within loop
				'-W082': true, // Declarations should not be placed in blocks
				'-W020': true, // Read only - error when assigning EO_SCRIPT_DEBUG a value.
			},
			all: [
				'assets/js/*.js',
				'!assets/js/*.min.js'
			]
		},

		// Watch for changes made in SASS.
		watch: {
			css: {
				files: [
					'assets/scss/*.scss',
				],
				tasks: ['sass', 'postcss']
			},
		},

		// Check for Sass errors with "stylelint"
		stylelint: {
			options: {
				configFile: '.stylelintrc'
			},
			all: [
				'assets/scss/**/*.scss',
			]
		},

		// Bump version numbers (replace with version in package.json)
		replace: {
			readme: {
				src: [
					'readme.txt',
					'README.md'
				],
				overwrite: true,
				replacements: [
					{
						from: /Requires at least:(\*\*|)(\s*?)[0-9.-]+(\s*?)$/mi,
						to: 'Requires at least:$1$2<%= pkg.requires %>$3'
					},
					{
						from: /Requires PHP:(\*\*|)(\s*?)[0-9.-]+(\s*?)$/mi,
						to: 'Requires PHP:$1$2<%= pkg.requires_php %>$3'
					},
					{
						from: /Tested up to:(\*\*|)(\s*?)[0-9.-]+(\s*?)$/mi,
						to: 'Tested up to:$1$2<%= pkg.tested_up_to %>$3'
					},
				]
			}
		},

		// Copies the plugin to create deployable plugin.
		copy: {
			build: {
				files: [
					{
						expand: true,
						src: [
							'**',
							'!.*',
							'!**/*.{gif,jpg,jpeg,js,json,log,md,png,scss,sh,txt,xml,zip}',
							'!.*/**',
							'!.DS_Store',
							'!.htaccess',
							'!assets/scss/**',
							'!assets/**/*.scss',
							'!<%= pkg.name %>-git/**',
							'!<%= pkg.name %>-svn/**',
							'!node_modules/**',
							'!releases/**',
							'readme.txt',
							'assets/css/**',
							'assets/js/**'
						],
						dest: 'build/',
						dot: true
					}
				]
			}
		},

		// Compresses the deployable folder.
		compress: {
			zip: {
				options: {
					archive: './releases/<%= pkg.name %>-v<%= pkg.version %>.zip',
					mode: 'zip'
				},
				files: [
					{
						expand: true,
						cwd: './build/',
						src: '**',
						dest: '<%= pkg.name %>'
					}
				]
			}
		},

		// Deletes the deployable folder once zipped up.
		clean: {
			build: [ 'build/' ]
		}
	});

	// Set the default grunt command to run test cases.
	grunt.registerTask( 'default', [ 'test' ] );

	// Checks for developer dependencie updates.
	grunt.registerTask( 'check', [ 'devUpdate' ] );

	// Checks for errors.
	grunt.registerTask( 'test', [ 'stylelint', 'jshint' ]);

	// Build CSS, minify CSS, minifiy JS and runs i18n tasks.
	grunt.registerTask( 'build', [ 'sass', 'postcss', 'cssmin', 'newer:uglify' ]);

	// Update version of script.
	grunt.registerTask( 'version', [ 'replace' ] );

	// Creates a deployable zipped up file.
	grunt.registerTask( 'zip', [ 'copy:build', 'compress', 'clean:build' ]);
};

var gulp = require('gulp');
var gutil = require('gulp-util');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var del = require('del');
var pathmap = require('gulp-pathmap');
var rjs = require('gulp-rjs');
var requirejs = require('requirejs');
var program = require('commander');

//path
var wamp_root = 'C:/wamp/www/prop';
var apache_root = 'D:/Develop/xampp/htdocs/prop';
var xampp_root = '/opt/lampp/htdocs/prop';
var tomcat_root = './dist/www';

var src_root = './www', des_root = './dist/www';

//set up
(function setup(){
	program
		.version('0.0.1')
		.option('-d, --debug', 'debug mode')
		.option('-a, --apache', 'output to apache server')
		.option('-t, --tomcat', 'output to tomcat server')
		.option('-w, --wamp', 'output to tomcat server')
		.option('-x, --xampp', 'output to xampp server')
		.parse(process.argv);

	if(program.apache){
		des_root = apache_root;
	} else if(program.wamp){
		des_root = wamp_root;
	} else if(program.xampp){
		des_root = xampp_root;
	} else if(program.tomcat){
		des_root = tomcat_root;
	}
})();
console.log('des_root: ' + des_root);

// src config
var src_dir_js = src_root + '/js';
var src_dir_lib = src_root + '/lib';
var src_dir_img = src_root + '/img';
var src_dir_css = src_root + '/css';
var src_dir_fonts = src_root + '/fonts';
var src_dir_data = src_root + '/data';
var src_dir_php = src_root + '/php';
var src_dir_pages = src_root + '/pages';
var src_dir_partials = src_root + '/partials';
var src_file_index = src_root + '/index.html';
var src_file_index_product = src_root + '/index_product.html';

// dest config
var des_dir_js = des_root + '/js';
var des_dir_lib = des_root + '/lib';
var des_dir_img = des_root + '/img';
var des_dir_css = des_root + '/css';
var des_dir_fonts = des_root + '/fonts';
var des_dir_data = des_root + '/data';
var des_dir_php = des_root + '/php';
var des_dir_pages = des_root + '/pages';
var des_dir_partials = des_root + '/partials';
var des_file_index = des_root + '/index.html';
var des_file_js_concat = 'main.js';
var des_file_deps_concat = 'deps.min.js';

// rjs config
var requirejs_config = {
	baseUrl: 'www/js',
	//dir: 'dist/www/js',
	dir: des_dir_js,
	name: 'main',
	fileExclusionRegExp: /^(r|build)\.js$/,
	// uglify2, uglify, none
	optimize: 'none',
	optimizeCss: 'standard',
	removeCombined: true,
	uglify2: {
		output: {
			beautify: true
		},
		compress: {
			sequences: false,
			global_defs: {
				DEBUG: false
			}
		},
		warnings: true,
		mangle: false
	}

};

var tasks = {
	del: function() {	
		del.sync([des_root], {
			force: true
		});
	},
	del_build_txt: function() {
		setTimeout(function() {
			del.sync([des_dir_js + '/build.txt'], {
				force: true
			});
		}, 500);
	},
	concat_lib: function() {
		gulp.src([
			src_dir_lib + '/jquery-2.0.3.min.js',
			src_dir_lib + '/bootstrap.js',
			src_dir_lib + '/headroom.min.js',
			src_dir_lib + '/hammer.js',
			src_dir_lib + '/iscroll.js',
			src_dir_lib + '/angular.js',
			src_dir_lib + '/angular-route.js',
			src_dir_lib + '/angular-resource.js',
			src_dir_lib + '/angular-hammer.js',
			src_dir_lib + '/ratchet.min.js'
		])
			.pipe(uglify())
			.pipe(concat(des_file_deps_concat))
			.pipe(gulp.dest(des_dir_js));
		gulp.src(src_dir_lib + '/require.js')
			.pipe(gulp.dest(des_dir_js));
	},
	concat_lib_debug: function() {
		gulp.src([
			src_dir_lib + '/jquery-2.0.3.min.js',
			src_dir_lib + '/bootstrap.js',
			src_dir_lib + '/headroom.min.js',
			src_dir_lib + '/hammer.js',
			src_dir_lib + '/iscroll.js',
			src_dir_lib + '/angular.js',
			src_dir_lib + '/angular-route.js',
			src_dir_lib + '/angular-resource.js',
			src_dir_lib + '/angular-hammer.js',
			src_dir_lib + '/ratchet.js'
		])
			.pipe(gulp.dest(des_dir_js));
		gulp.src(src_dir_lib + '/require.js')
			.pipe(gulp.dest(des_dir_js));
	},
	copy: function(debug) {
		if(!!debug){
			gulp.src(src_file_index).pipe(pathmap('www/index.html')).pipe(gulp.dest(des_root));
		}else {
			gulp.src(src_file_index_product).pipe(pathmap('www/index.html')).pipe(gulp.dest(des_root));
		}
		gulp.src(src_dir_img + '/**/*.*').pipe(gulp.dest(des_dir_img));
		gulp.src(src_dir_data + '/**/*.*').pipe(gulp.dest(des_dir_data));
		gulp.src(src_dir_php + '/**/*.*').pipe(gulp.dest(des_dir_php));
		gulp.src(src_dir_partials + '/**/*.*').pipe(gulp.dest(des_dir_partials));
		gulp.src(src_dir_css + '/**/*.*').pipe(gulp.dest(des_dir_css));
		gulp.src(src_dir_fonts + '/**/*.*').pipe(gulp.dest(des_dir_fonts));
	},
	optimize: function(cb) {
		requirejs.optimize(requirejs_config);
		if (typeof cb === 'function')
			cb();
	}
};

gulp.task('clean', tasks.del);
gulp.task('concat_lib', [], tasks.concat_lib);
gulp.task('concat_lib_debug', [], tasks.concat_lib_debug);
gulp.task('copy', [], function(){tasks.copy(false)});
gulp.task('copy_debug', [], function(){tasks.copy(true)});
gulp.task('optimize', [], tasks.optimize);
gulp.task('del_build_txt', ['optimize'], tasks.del_build_txt);
gulp.task('default', ['clean', 'concat_lib', 'copy', 'optimize', 'del_build_txt']);
gulp.task('debug', ['clean', 'concat_lib_debug', 'copy_debug', 'optimize', 'del_build_txt']);
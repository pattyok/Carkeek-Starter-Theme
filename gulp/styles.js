/* eslint-env es6 */
'use strict';

/**
 * External dependencies
 */
import { src, dest } from 'gulp';
import postcssPresetEnv from 'postcss-preset-env';
import postcssNesting from 'postcss-nesting';
import AtImport from 'postcss-import';
import pump from 'pump';
import cssnano from 'cssnano';
import stylelint from 'stylelint';
import reporter from 'postcss-reporter';
import calc from 'postcss-calc';
import { pipeline } from 'mississippi';

/**
 * Internal dependencies
 */
import { rootPath, paths, gulpPlugins, isProd, assetsDir } from './constants';
import {
	getThemeConfig,
	getStringReplacementTasks,
	logError,
	configValueDefined,
	appendBaseToFilePathArray,
} from './utils';
import { server } from './browserSync';

export function stylesBeforeReplacementStream() {
	// Return a single stream containing all the
	// before replacement functionality
	return pipeline.obj( [
		logError( 'CSS' ),
		gulpPlugins.newer( {
			dest: paths.styles.dest,
			extra: [ paths.config.themeConfig ],
		} ),
		gulpPlugins.phpcs( {
			bin: `${ rootPath }/vendor/bin/phpcs`,
			standard: 'WordPress',
			warningSeverity: 0,
		} ),
		// Log all problems that were found.
		gulpPlugins.phpcs.reporter( 'log' ),
	] );
}

export function stylesAfterReplacementStream() {
	const config = getThemeConfig();

	const postcssPlugins = [
		stylelint(),
		postcssNesting(),
		postcssPresetEnv( {
			importFrom: (
				configValueDefined( 'config.dev.styles.importFrom' ) ?
					appendBaseToFilePathArray( config.dev.styles.importFrom, paths.styles.srcDir ) :
					[]
			),
			stage: (
				configValueDefined( 'config.dev.styles.stage' ) ?
					config.dev.styles.stage :
					3
			),
			autoprefixer: (
				configValueDefined( 'config.dev.styles.autoprefixer' ) ?
					config.dev.styles.autoprefixer :
					{}
			),
			features: (
				configValueDefined( 'config.dev.styles.features' ) ?
					config.dev.styles.features :
					{
						'custom-media-queries': {
							preserve: false,
						},
						'custom-properties': {
							preserve: true,
						},
						'nesting-rules': true,
					}
			),
		} ),
		calc( {
			preserve: false,
		} ),
		cssnano(),
	];

	// Skip minifying files if we aren't building for
	// production and debug is enabled
	if ( config.dev.debug.styles && ! isProd ) {
		postcssPlugins.pop();
	}

	// Report messages from other postcss plugins
	postcssPlugins.push(
		reporter( { clearReportedMessages: true } )
	);

	// Return a single stream containing all the
	// after replacement functionality
	return pipeline.obj( [
		gulpPlugins.postcss( [
			AtImport( {
				path: [ paths.styles.srcDir ],
				plugins: [
					stylelint(),
				],
			} ),
		] ),
		gulpPlugins.postcss( postcssPlugins ),
		gulpPlugins.if(
			config.dev.debug.styles,
			gulpPlugins.tabify( 2, true )
		),
		gulpPlugins.rename( {
			suffix: '.min',
		} ),
		// gulpPlugins.if(
		// 	isProd,
		// 	writeToManifest()
		// ),
		server.stream( { match: '**/*.css' } ),

	] );
}

// export function writeToManifest() {
// 	return pipeline.obj( [
// 		gulpPlugins.rev(),
// 		dest(paths.styles.dest),
// 		gulpPlugins.rev.manifest( {
// 			base: assetsDir,
// 			merge: true
// 		}),
// 		dest(assetsDir),
// 	]);
// }

/**
* CSS via PostCSS + CSSNext (includes Autoprefixer by default).
* @param {function} done function to call when async processes finish
* @return {Stream} single stream
*/
export default function styles( done ) {
	return pump( [
		src( paths.styles.src, { sourcemaps: ! isProd } ),
		stylesBeforeReplacementStream(),
		// Only do string replacements when building for production
		gulpPlugins.if(
			isProd,
			getStringReplacementTasks()
		),
		stylesAfterReplacementStream(),
		dest( paths.styles.dest, { sourcemaps: ! isProd } ),
	], done );
}

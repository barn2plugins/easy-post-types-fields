const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const Barn2Configuration = require( '@barn2plugins/webpack-config' );

const config = new Barn2Configuration(
	[
		'admin/editor/index.js',
		'admin/wizard/index.js',
		'admin/wizard-library/index.js'
	],
	[
		'admin/ept-editor.scss',
		'admin/ept-post-editor.scss'
	],
	defaultConfig
);

module.exports = config.getWebpackConfig();
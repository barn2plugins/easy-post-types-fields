const pluginData = {
	name: 'Easy Post Types and Fields',
	libNamespace: 'Barn2\\EPT_Lib',
	libIncludes: [ 'Plugin/Plugin.php', 'Plugin/Simple_Plugin.php', 'Plugin/Plugin_Activation_Listener.php', '*.php', 'Admin/**', 'assets/css/**', 'assets/js/**', '!class-*.php' ],
	requiresES6: true
};

const fs = require( 'fs' ),
	  barn2build = getBarn2Build();

function getBarn2Build() {
	var build;

	if ( fs.existsSync( '../barn2-lib/build' ) ) {
		build = require( '../barn2-lib/build/gulpfile-common' );
	} else if ( process.env.BARN2_LIB ) {
		build = require( process.env.BARN2_LIB + '/build/gulpfile-common' );
	} else {
		throw new Error( "Error: please set the BARN2_LIB environment variable to path of Barn2 Library project" );
	}

	build.setupBuild( pluginData );

	return build;
}

function test( cb ) {
	console.log( 'All looks good.' );
	cb();
}

module.exports = {
	default: test,
	build: barn2build.buildPlugin,
	assets: barn2build.buildAssets,
	pot: barn2build.buildTranslation,
	library: barn2build.updateLibrary,
	zip: barn2build.createZipFile,
	archive: barn2build.archivePlugin,
	release: barn2build.releaseFreePlugin,
	pluginTesting: barn2build.updatePluginTesting,
	playground: barn2build.updatePluginPlayground,
	wizard: barn2build.updateSetupWizard
};
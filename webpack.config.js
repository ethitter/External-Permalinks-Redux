const config = require( './node_modules/@wordpress/scripts/config/webpack.config' );
const { resolve } = require( 'path' );

config.entry = {
	index: './assets/src/index.js',
};

config.output.path = resolve( process.cwd(), 'assets/build' );

module.exports = config;

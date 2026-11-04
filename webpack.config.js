const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// Remove SASS rule from the default config so we can define our own.
const defaultRules = defaultConfig.module.rules.filter((rule) => {
	return String(rule.test) !== String(/\.(sc|sa)ss$/);
});

module.exports = {
	...defaultConfig,
	entry: {
		'frontend/index': path.resolve(process.cwd(), 'src', 'index.js'),
		'frontend/orddd-block': path.resolve(process.cwd(), 'src', 'frontend', 'index.js'),
		'frontend/orddd-block-frontend': path.resolve(process.cwd(), 'src', 'frontend', 'frontend.js'),
		'frontend/cart-block': path.resolve(process.cwd(), 'src', 'frontend', 'cart-block', 'cart-index.js'),
		'frontend/cart-block-frontend': path.resolve(process.cwd(), 'src', 'frontend', 'cart-block', 'frontend.js'),
		'admin/index': path.resolve(process.cwd(), 'src', 'admin', 'index.js'),
	},
	module: {
		...defaultConfig.module,
		rules: [
			...defaultRules,
			{
				test: /\.(sc|sa)ss$/,
				exclude: /node_modules/,
				use: [
					MiniCssExtractPlugin.loader,
					{ loader: 'css-loader', options: { importLoaders: 1 } },
					{
						loader: 'sass-loader',
						options: {
							sassOptions: {
								includePaths: ['src/frontend/css'],
							},
							additionalData: (content) => content,
						},
					},
				],
			},
		],
	},
	plugins: [
		...defaultConfig.plugins.filter(
			(plugin) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin(),
		new MiniCssExtractPlugin({
			filename: `[name].css`,
		}),
	],
	externals: {
		...defaultConfig.externals,
		'@wordpress/api-fetch': 'wp.apiFetch',
		'@wordpress/i18n': 'wp.i18n',
	},
};

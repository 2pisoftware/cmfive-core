// vite.config.js

import vue from "@vitejs/plugin-vue";
import fs from "fs";
import { glob } from "glob";
import path, { resolve } from "path";
import { build, defineConfig } from "vite";
import { viteStaticCopy } from "vite-plugin-static-copy";

// We need to determine the root directory of the project - since everything is symlinked this requires a bit of thought
let scriptPath = __dirname;

console.log("dirname", scriptPath);
/* Apply conditional steps to untangle, eg:
    dirname /workspaces/cmfive_dev_box/cmfive-core/system/templates/base
    scriptPath 2 /workspaces/cmfive_dev_box/
    scriptPath SHOULD BE : /workspaces/cmfive_dev_box/cmfive-boilerplate/
    _vs_
    dirname /codebuild/output/src3846646311/src/composer/vendor/2pisoftware/cmfive-core/system/templates/base
    scriptPath 2 /codebuild/output/src3846646311/src/composer/vendor/2pisoftware/
    scriptPath SHOULD BE : /codebuild/output/src3846646311/src
    _etc_ ...
    */
if (scriptPath.includes("cmfive-boilerplate")) {
	// System is symlinked inside of boilerplate
	scriptPath =
		scriptPath.split("cmfive-boilerplate")[0] + "cmfive-boilerplate/";
} else if (scriptPath.includes("cmfive-core")) {
	// System is symlinked outside of boilerplate
	scriptPath = scriptPath.split("cmfive-core")[0];
	console.log("scriptPath 2", scriptPath);
	if (fs.existsSync(scriptPath + "cmfive-boilerplate")) {
		// we are in some assembled/mounted project
		scriptPath += "cmfive-boilerplate/";
	} else if (fs.existsSync("/var/www/html")) {
		// we are in hosted folders
		scriptPath = "/var/www/html/";
	} else if (fs.existsSync("/codebuild/output")) {
		// we are in a cdk pipeline
		scriptPath = scriptPath.split("composer")[0];
	} else {
		throw new Error("Could not determine root directory of project");
	}
} else if (fs.existsSync("/var/www/html")) {
	// we are in hosted folders
	scriptPath = "/var/www/html/";
}

console.log("scriptPath", scriptPath);

const _x = [
	resolve(__dirname, "src/js/app.ts").replace(/\\/g, "/"),
	resolve(__dirname, "src/scss/app.scss").replace(/\\/g, "/"),
	...glob.sync(
		resolve(__dirname, scriptPath, "system/modules/**/assets/ts/*.ts").replace(
			/\\/g,
			"/",
		),
	),
	...glob.sync(
		resolve(
			__dirname,
			scriptPath,
			"system/modules/**/assets/scss/*.scss",
		).replace(/\\/g, "/"),
	),
	...glob.sync(
		resolve(__dirname, scriptPath, "modules/**/assets/ts/*.ts").replace(
			/\\/g,
			"/",
		),
	),
	...glob.sync(
		resolve(__dirname, scriptPath, "modules/**/assets/scss/*.scss").replace(
			/\\/g,
			"/",
		),
	),
];

const _fileMapObj = {};
_x.forEach((file) => {
	let name = file.replace(/\\/g, "/").split("/").pop().split(".").shift();
	const ext = file.replace(/\\/g, "/").split(".").pop();

	if (Object.hasOwn(_fileMapObj, `${name}`)) {
		name = `${name}.${ext}`;
	}
	_fileMapObj[`${name}`] = file.replace(/\\/g, "/");
});

console.log("fileMap", _fileMapObj);

export default defineConfig({
	plugins: [
		vue(),
		viteStaticCopy({
			targets: [
				{
					src: resolve(
						__dirname,
						scriptPath,
						"system/templates/base/node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff",
					).replace(/\\/g, "/"),
					dest: "fonts",
				},
				{
					src: resolve(
						__dirname,
						scriptPath,
						"system/templates/base/node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff2",
					).replace(/\\/g, "/"),
					dest: "fonts",
				},
			],
		}),
	],
	build: {
		cssCodeSplit: true,
		minify: "terser",
		target: "modules",
		lib: {
			entry: _fileMapObj,
			formats: ["es"],
		},
		rollupOptions: {
			output: {
				format: "es",
				assetFileNames: (assetInfo) => {
					if (assetInfo.name === "style.css") return "app.css";
					return assetInfo.name;
				},
			},
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				includePaths: [
					scriptPath + "system/templates/base/src/scss/",
					scriptPath + "system/templates/base/node_modules",
				],
			},
		},
	},
	define: {
		"process.env": {
			rootPath: scriptPath,
		},
	},
	resolve: {
		alias: {
			"~": resolve(
				__dirname,
				scriptPath + "system/templates/base/node_modules",
			).replace(/\\/g, "/"),
			"@": resolve(__dirname, scriptPath + "system/templates/base/src").replace(
				/\\/g,
				"/",
			),
			vue: "vue/dist/vue.esm-bundler.js",
		},
	},
});

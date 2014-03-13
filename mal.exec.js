/*!
 * MyAnimeList Data Collector application based at CasperJS.
 *
 * Version: 1.0.0
 * Usage: node mal.exec.js --url "http://myanimelist.net/manga/{id}"
 *
 * Copyright (c) 2014 k3nsei.pl@gmail.com
 *
 */
var stdio = require('stdio');
var exec = require('child_process').exec,
	child;

var ops = stdio.getopt({
	'url': {key: 'u', args: 1, mandatory: true}
});

var siteUrl = null;

if (ops.url) {
	siteUrl = ops.url;
	var urlPattern = siteUrl.match(/http:\/\/(www\.|)myanimelist\.net\/(manga|people)\/(\d+)/);
	if (urlPattern !== null) {
		child = exec('casperjs mal.casper.js ' + siteUrl,
			function (error, stdout) {
				console.log(stdout);
				if (error !== null) {
					console.log('exec error: ' + error);
				}
			}
		);
	} else {
		console.log("Invalid url...");
	}
}

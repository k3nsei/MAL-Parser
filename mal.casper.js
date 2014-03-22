/*!
 * MyAnimeList Data Collector application based at CasperJS.
 *
 * Version: 1.0.5
 * Usage: casperjs mal.casper.js "http://myanimelist.net/manga/{id}"
 *
 * Copyright (c) 2014 k3nsei.pl@gmail.com
 *
 */
var casper = require("casper").create({
	//verbose: true,
	//logLevel: "debug",
	viewportSize: {
		width: 400,
		height: 300
	},
	pageSettings: {
		userAgent: "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36"
	}
});

var siteUrl;
var urlPattern = null;

if (casper.cli.has(0)) {
	siteUrl = casper.cli.get(0);
} else {
	casper.echo("Musisz przekazac aplikacji poprawny adres url.").exit();
}

urlPattern = siteUrl.match(/http:\/\/(www\.|)myanimelist\.net\/(manga|people)\/(\d+)/);

if (urlPattern === null) {
	casper.echo("Przekazany adres url jest niezgodny z wzorcem.").exit();
} else {
	siteUrl = "http://myanimelist.net/" + urlPattern[2] + "/" + urlPattern[3];
	casper
		.start()
		.open(siteUrl)
		.then(function() {
			// do something
		})
		.run(function () {
			console.log(this.getHTML());
			this.exit(); // <--- don't forget me!
		});
}

var url = baseUrl + 'not-found/';
var scriptname = 'view-404-page';

casper.start(url, function()
{
    casper.test.info("URL: " + url);

});

casper.then(function()
{
    casper.test.info("\n--------------------------------------------");
    casper.test.info("GIVEN: an unauthenticated user");
    casper.test.info(" WHEN: they visit a page that does not exist");
    casper.test.info(" THEN: They should receive a 404 http status");

    this.test.assertHttpStatus(404);
});

casper.then(function()
{
    casper.test.info("\n--------------------------------------------");
    casper.test.info("GIVEN: an unauthenticated user");
    casper.test.info(" WHEN: they visit a page that does not exist");
    casper.test.info(" THEN: The charset should be set to UTF-8.");

    this.test.assertSelectorExists('meta[name="charset"]',
            'Check for meta tag charset.');
    this.test.assertSelectorExists('meta[name="charset"][content="utf-8"]',
            'Check charset should be set to utf-8');
});

casper.then(function ()
{
    casper.test.info("\n--------------------------------------------");
    casper.test.info("GIVEN: an unauthenticated user");
    casper.test.info(" WHEN: they visit a page that does not exist");
    casper.test.info(" THEN: They see a page with a header.");

    this.test.assertExists('header', 'Header section exists.');
});

casper.then(function()
{
    casper.test.info("\n--------------------------------------------");
    casper.test.info("GIVEN: an unauthenticated user");
    casper.test.info(" WHEN: they visit a page that does not exist");
    casper.test.info(" THEN: They see a page with a footer.");

    this.test.assertExists('footer', 'Footer section exists.');
});

casper.run(function()
{
    casper.test.info("\n--------------------------------------------");
    casper.test.done();
});
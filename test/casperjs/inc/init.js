var errorCount = 0;
var screenBase = 'error-screens/';
var viewportSize = {width: 1280, height: 1024};

casper.options.viewportSize = viewportSize;

if(undefined === scriptname)
{
  var scriptname = Date.now();
}

casper.test.on("fail", function(failure) {
  casper.capture(screenBase + scriptname + '-' + errorCount + '.png');
  casper.echo('Test Failure - screenshot: ' + screenBase + scriptname + '-' + errorCount + '.png', 'TRACE');
  errorCount++;
});

casper.options.onWaitTimeout = function() {
  casper.capture(screenBase + scriptname + '-' + errorCount + '.png');
  casper.echo('Timeout Failure - screenshot: ' + screenBase + scriptname + '-' + errorCount + '.png', 'TRACE');
  errorCount++;
};

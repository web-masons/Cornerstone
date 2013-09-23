Zend Studio 10
===========================
I'm generally a fan of Zend Studio 10, though I could rant for an hour about some of its quirks that drive me crazy.

Introduction
------------

Code Formatter
------------
I have attached my modified PSR-2 code formatter preferences that my code has been written with if you
would like to contribute back. You can update your version of Zend Studio 10 to use its settings by
following the steps outlined below. I use a MacBook, so these steps are for the Mac Client, though they
are similar on Windows as well.

# Open the Zend Studio menu
# Click `Preferences...`
# Type `Formatter` into the Search Box
# Click on the PHP -> Code Style -> Formatter entry
# Click `Import...`
# The rest should be fairly self-explanatory

To auto format your code while you have a file open you can use Cntl-Shift-F in Windows or Command-Shift-F on OS X.

Tips & Tricks: Annoying Vendor Warnings
------------
If you're like me and you absolutely hate to see "Problems" in your projects, but get annoyed because
vendor projects are always riddled with problems and warnings, there is hope.

This doesn't *always* work, but most of the time it does. I have no idea why it only works sometimes, much like
many of the Eclipse/Zend Studio 10 issues, you may have to do some sort of strange dance to make it work.

# Right-click on your `vendor` folder and select `Build Path` and then `Exclude`.
# Right-click on your `vendor` folder again and select `Include Path -> Configure Include Path...`
# Click on the `Libraries` tab
# Click on `Add External Source Folder...`
# Find your vendor folder in the browse window and select it
# This *should* fix the problem.

Tips & Tricks: Localization
------------
Always remember to go into Zend Studio and update your content types for text files.

# Open the Zend Studio menu
# Click `Preferences...`
# Type `Content Types` into the Search Box
# Click on the `Text` entry
# At the bottom of that window, where it says `Default Encoding`, type "UTF-8"
# Click Update

var cornerstone = require('cornerstone-grunt');

module.exports = function(grunt){
  var config = {
    mainCss: [
      "<%= module %>js/scripts.js"
    ],
    mainJs: [
      "<%= module %>css/<%= sassOut %>base.css"
    ]
  };
  cornerstone(grunt, config);
};
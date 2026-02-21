/* eslint-disable @typescript-eslint/no-var-requires */
/* eslint-disable no-undef */
module.exports = {
  plugins: [
    require('postcss-import')(),
    require('postcss-nested'),
    require('autoprefixer'),
  ],
};

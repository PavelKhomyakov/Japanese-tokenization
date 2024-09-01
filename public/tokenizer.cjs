const kuromoji = require('kuromoji');
const path = require('path');

const tokenizerPath = path.join(__dirname, '../node_modules/kuromoji/dict');

kuromoji.builder({ dicPath: tokenizerPath }).build(function (err, tokenizer) {
  if (err) {
    console.error('Error building tokenizer:', err);
    process.exit(1);
  }

  process.stdin.setEncoding('utf-8');
  process.stdin.on('data', (text) => {
    try {
      const tokens = tokenizer.tokenize(text.trim());
      process.stdout.write(JSON.stringify(tokens));
    } catch (e) {
      console.error('Error during tokenization:', e);
      process.exit(1);
    }
  });
});

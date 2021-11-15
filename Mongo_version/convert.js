const fs = require('fs');
let file = fs.readFileSync("/home/cs143/data/nobel-laureates.json");
let data = JSON.parse(file)
laureatesEntry = data.laureates;

for (entry of laureatesEntry) {
    appendToFile(JSON.stringify(entry) + "\n");
}

function appendToFile(content) {
    fs.appendFile('laureates.import', content, err => {
        if (err) {
            console.error(err);
            return;
        }
    })
}
db.laureates.aggregate([
	{ $unwind: "$nobelPrizes" },
	{ $unwind: "$nobelPrizes.affiliations" },
	{ $match: { "nobelPrizes.affiliations.name.en" : "University of California"} },
	{ $project: { _id: 0, "name" : "$nobelPrizes.affiliations.name.en" , "city" : "$nobelPrizes.affiliations.city.en", "country" : "$nobelPrizes.affiliations.country.en"} },
	{ $group : { _id : {$concat : [ "$city", ", ", "$country" ]} } },
	{ $count : "locations"}
	]).pretty();

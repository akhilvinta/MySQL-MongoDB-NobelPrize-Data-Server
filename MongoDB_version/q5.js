db.laureates.aggregate([
	{ $match: { "orgName": { $ne: null } } },
	{ $unwind: "$nobelPrizes" },
	{ $project: { _id : 0, id : 1, "year" : "$nobelPrizes.awardYear"} },
	{ $group: { _id: "$year"} },
	{ $count: "years"}
	]).pretty();
db.laureates.aggregate([
	{ $unwind: "$nobelPrizes" },
	{ $group : { _id:"$familyName.en", "count":{$sum:1} } },
	{ $match: { "_id" : { $ne: null } } },
	{ $match: { "count" : { $gte: 5 } } },
	{ $project: { "familyName": "$_id", _id: 0 } }
	]).pretty();
import json
from os import name

def getLaureateAttributes(laureate):
    #Initilaize all to NULL
    id = "\\N" 
    birth_date = "\\N"
    birth_city = '\\N'
    birth_country = '\\N'

    if 'id' in laureate:
        id = laureate['id']
    if 'birth' in laureate:
        if 'date' in laureate['birth']:
            birth_date = laureate['birth']['date']
        if 'place' in laureate['birth']:
            if 'city' in laureate['birth']['place']:
                birth_city = laureate['birth']['place']['city']['en']
            if 'country' in laureate['birth']['place']:
                birth_country = laureate['birth']['place']['country']['en']
    elif 'founded' in laureate:
        if 'date' in laureate['founded']:
            birth_date = laureate['founded']['date']
        if 'place' in laureate['founded']:
            if 'city' in laureate['founded']['place']:
                birth_city = laureate['founded']['place']['city']['en']
            if 'country' in laureate['founded']['place']:
                birth_country = laureate['founded']['place']['country']['en']
    
    return id,birth_date,birth_city,birth_country

def getPersonAttributes(laureate):
    #Initialize all to NULL
    id = "\\N"
    given_name = "\\N"
    family_name = "\\N"
    gender = "\\N"

    if 'id' in laureate:
        id = laureate['id']

    if 'givenName' in laureate:
        given_name = laureate['givenName']['en']
    if 'familyName' in laureate:
        family_name = laureate['familyName']['en']
    if 'gender' in laureate:
        gender = laureate['gender']

    return id,given_name,family_name,gender

def getOrgAttributes(laureate):
    id = "\\N"
    orgName = "\\N"

    if 'id' in laureate:
        id = laureate['id']
    
    if 'orgName' in laureate:
        orgName = laureate['orgName']['en']

    return id,orgName


def getPrizeAttributes(prize):
    award_year = '\\N'
    category = '\\N'
    sortOrder = '\\N'
    if 'awardYear' in prize:
        award_year = prize['awardYear']
    if 'category' in prize:
        category = prize['category']['en']
    if 'sortOrder' in prize:
        sortOrder = prize['sortOrder']

    return award_year, category, sortOrder

def getAffiliationAttributes(affiliaiton):
    name = '\\N'
    city = '\\N'
    country = '\\N'

    if 'name' in affiliation:
        name = affiliation['name']['en']
    if 'city' in affiliation:
        city = affiliation['city']['en']
    if 'country' in affiliation:
        country = affiliation['country']['en']

    return name, city, country




# load data
data = json.load(open("/home/cs143/data/nobel-laureates.json", "r"))

#sets to keep track of different keys.
#Map a laureate id to its attributes
laureates = {}

#map a person's id to its attributes
people = {}

#map a organizations id to its attributes
organizations = {}

#map a prizes attributes to its id
prizes = {}

#map an affiliation's attributes to its id
affiliations = {}

#Awarded
awarded = set()

# get the id, givenName, and familyName of the first laureate
# laureate = data["laureates"][0]
# id = laureate["id"]
# givenName = laureate["givenName"]["en"]
# familyName = laureate["familyName"]["en"]

# # print the extracted information
# print(id + "\t" + givenName + "\t" + familyName)
print('Building the files...')

#The files we will be outputting our data to, in a format that can be bulk-loaded by SQL commands.
LaureateFile = open('./laureates.del','x')
PersonFile = open('./people.del','x')
OrgFile = open('./orgs.del', 'x')
PrizeFile = open('./prizes.del', 'x')
AffiliationFile = open('./affiliations.del', 'x')
AwardedFile = open('./awarded.del', 'x')

#Go through all the laureates and build the tables.
for laureate in data['laureates']:
    #Create an entry for the Laureate table, and either for the Person Table or the Organization table
    id,birth_date,birth_city,birth_country = getLaureateAttributes(laureate)
    if 'orgName' in laureate:
        #This is a organization
        id,orgName = getOrgAttributes(laureate)
        #Ensure no duplicates
        if not id in organizations:
            organizations[id] = (id,orgName)
            OrgFile.write(f'{id}|{orgName}\n')
    else:     
        #This is a person
        id,given_name,family_name,gender = getPersonAttributes(laureate)
        #Ensure no duplicates
        if not id in people:
            people[id] = (id,given_name,family_name,gender)
            PersonFile.write(f'{id}|{given_name}|{family_name}|{gender}\n')

    #Ensure no duplicates
    if not id in laureates:
        laureates[id] = (id,birth_date,birth_city,birth_country)
        LaureateFile.write(f'{id}|{birth_date}|{birth_city}|{birth_country}\n')
    
    laureate_id = id

    #For every prize won by this laureate, create an entry
    for prize in laureate['nobelPrizes']:
        award_year, category, sortOrder = getPrizeAttributes(prize)
        if not (award_year,category,sortOrder) in prizes:
            prize_id = len(prizes)
            prizes[(award_year,category,sortOrder)] = prize_id
            PrizeFile.write(f'{prize_id}|{award_year}|{category}|{sortOrder}\n')

        prize_id =  prizes[(award_year,category,sortOrder)]

        #For every affiliation for this prize, create an entry.
        #Account for the case where there are no affiliations
        if not 'affiliations' in prize:
            affiliation_id = "\\N"
            if not(laureate_id,prize_id,affiliation_id) in awarded:
                    awarded.add((laureate_id,prize_id,affiliation_id))
                    AwardedFile.write(f'{laureate_id}|{prize_id}|{affiliation_id}\n')

        else:
            for affiliation in prize["affiliations"]:
                name, city, country = getAffiliationAttributes(affiliation)
                if not (name,city,country) in affiliations:
                    affiliation_id = len(affiliations)
                    affiliations[(name,city,country)] = affiliation_id
                    AffiliationFile.write(f'{affiliation_id}|{name}|{city}|{country}\n')

                affiliation_id = affiliations[(name,city,country)]

                #Make the final Awards table
                if not(laureate_id,prize_id,affiliation_id) in awarded:
                    awarded.add((laureate_id,prize_id,affiliation_id))
                    AwardedFile.write(f'{laureate_id}|{prize_id}|{affiliation_id}\n')


                    





LaureateFile.close()
PersonFile.close()
OrgFile.close()
PrizeFile.close()
AffiliationFile.close()
AwardedFile.close()






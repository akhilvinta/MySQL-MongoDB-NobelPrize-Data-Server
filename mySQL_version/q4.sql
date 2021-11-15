select count(id)
from Affiliation
where name = 'University of California' and city is not null and country is not null;
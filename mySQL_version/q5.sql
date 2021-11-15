--get all prizes awarded to organizations, and then get all distinct years in which an organization won a prize, 
--i.e won in at least one category

select count(distinct award_year)
from Prize , Awarded
where laureate_id in (select id from Organization) and id = prize_id
;

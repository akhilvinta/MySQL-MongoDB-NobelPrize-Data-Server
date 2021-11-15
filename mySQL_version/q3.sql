select family_name 
from Person, Awarded
where laureate_id = id
group by family_name
having count(distinct prize_id)>=5;
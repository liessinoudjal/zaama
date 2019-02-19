insert into ville (departement_id,code_postal,nom,longitude,latitude)
SELECT ville_departement,
		ville_code_postal,
        ville_nom_reel,
        ville_longitude_deg,
        ville_latitude_deg
FROM `villes_france_free` 
where ville_departement in (75,77,78,91,92,93,94,95)
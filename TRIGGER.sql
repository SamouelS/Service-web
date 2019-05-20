/*Trigger sur Infirmiere badge*/

CREATE TRIGGER `changeActif` AFTER UPDATE ON `infirmiere_badge`
 FOR EACH ROW IF new.date_fin IS NOT NULL
THEN
	update badge set badge.actif=0 WHERE badge.id = new.id_badge;
END IF

CREATE TRIGGER `verifBadge` BEFORE INSERT ON `infirmiere_badge`
 FOR EACH ROW if (select infirmiere_badge.id_infirmiere FROM infirmiere_badge WHERE infirmiere_badge.id_infirmiere = new.id_infirmiere and infirmiere_badge.date_fin IS NULL) IS NOT NULL
	THEN
    	DELETE FROM infirmiere_badge WHERE infirmiere_badge.id_infirmiere = new.id_infirmiere;
    ELSE
    	UPDATE badge set actif = 1 WHERE badge.id = new.id_badge;
end if
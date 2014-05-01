-- This creates a view we will use for the anniversary feature
DROP VIEW IF EXISTS pm_data;
CREATE VIEW pm_data AS
    SELECT
        id,
        DATE_FORMAT(FROM_UNIXTIME(starttime), '%m-%d') AS thedate,
        deleted
FROM postmortems WHERE deleted = 0;

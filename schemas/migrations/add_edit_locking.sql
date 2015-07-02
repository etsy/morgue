alter table postmortems add column created int(11) UNSIGNED NOT NULL;
alter table postmortems add column modified int(11) UNSIGNED NOT NULL DEFAULT '0';
alter table postmortems add column modifier varchar(255) NOT NULL DEFAULT '';

update postmortems set created = (select IFNULL(MIN(create_date), UNIX_TIMESTAMP()) as create_date from postmortem_history where postmortem_history.postmortem_id = postmortems.id);

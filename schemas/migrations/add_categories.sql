alter table postmortems add column subsystem varchar(50) NOT NULL DEFAULT '';
alter table postmortems add column owner_team varchar(50) NOT NULL DEFAULT '';
alter table postmortems add column problem_type varchar(50) NOT NULL DEFAULT '';
alter table postmortems add column impact_type varchar(50) NOT NULL DEFAULT '';
alter table postmortems add column incident_cause varchar(50) NOT NULL DEFAULT '';

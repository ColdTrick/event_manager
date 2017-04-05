<?php

return array(
	'event_manager' => 'Event Manager',
	'groups:enableevents' => 'Activer évènements de groupe',
	'event_manager:group' => 'Evènements de groupe',
	'event_manager:group:more' => 'Plus d\'évènements',
	
	'admin:upgrades:migrate_files_to_event' => 'Migration de fichiers de Event Manager',
	'admin:upgrades:migrate_files_to_event:description' => 'Tout les fichiers liés ont besoin d\'être migrés pour être stockés avec les événements au lieu de se trouver dans le répertoire data de l\'utilisateur.',
	'admin:upgrades:convert_timestamps' => 'conversion du timestamp de l\'Event Manager',
	'admin:upgrades:convert_timestamps:description' => 'le stockage des timestamp à changés de champs metadata',

	'item:object:event' => 'Evènements',
	'item:object:eventslot' => 'Evènement emplacements',
	'item:object:eventday' => 'Evènement jour',
	'item:object:eventregistration' => 'Evènement enregistrements',
	'item:object:eventregistrationquestion' => 'Questions pour s\'inscrire à un évènement',
	'item:object:eventquestions' => 'Questions évènement',
	
	'event_manager:date:format' => 'Y-m-d',
	'event_manager:enity:copy' => 'Copie de: %s',
	
	'event_manager:menu:title' => 'Evènements',
	'event_manager:menu:events' => 'Tous les évènements',
	'event_manager:menu:group_events' => 'Evènements de groupe',
	'event_manager:menu:new_event' => 'Créer évènement',
	'event_manager:menu:copy' => 'Copier l\'événement',
	'event_manager:menu:unsubscribe' => "Description",
	'event_manager:menu:unsubscribe_confirm' => "Confirmé la désinscription",
	'event_manager:menu:registration:completed' => "Enregistrement de l\'événement réalisé",
	'event_manager:menu:attending' => "Participants",
	
	'event_manager:notification:subject' => "Un événement à été créé",
	'event_manager:notification:summary' => "Un événement à été créé",
	'event_manager:notification:body' => "%s a créé à nouvel événement %s",
	
	'event_manager:sidebar:title' => 'Actuellement affiché',
	
	'river:comment:object:event' => '%s a commenté %s',
	
	'event_manager:calendar:today' => 'Aujourd\'hui',
	'event_manager:calendar:month' => 'Mois',
	'event_manager:calendar:week' => 'Semaine',
	'event_manager:calendar:day' => 'Jour',

	'event_manager:list:title' => 'Evènements',
	'event_manager:list:group:title' => 'Événements de groupe',
	'event_manager:list:advancedsearch' => 'Recherche avancée',
	'event_manager:list:simplesearch' => 'Recherche simple',
	'event_manager:list:noresults' => 'Aucuns évènements trouvé',
	'event_manager:list:showmorevents' => 'Afficher plus d\'évènements',
	'event_manager:list:includepastevents' => 'Inclure les évènements passés',
	'event_manager:list:attending' => 'Montrer les évènements auxquels je participe',
	'event_manager:list:owning' => 'Montrer les évènements que j\'ai créé',
	'event_manager:list:friendsattending' => 'Montrer les évènements auxquels mes contacts participent',
	
	'event_manager:list:navigation:list' => 'Liste',
	'event_manager:list:navigation:calendar' => 'Calendrier',
	'event_manager:list:navigation:other' => 'Autres évènements',
	'event_manager:list:navigation:your' => 'Vos évènements',
	'event_manager:list:navigation:attending' => 'Participes aux évènements',
	'event_manager:list:navigation:onthemap' => 'Sur la carte',
	
	'event_manager:owner:title' => 'Événements de %s',
	'event_manager:attending:title' => 'Événement %s est en attente',
	
	'event_manager:full' => 'tout',
	'event_manager:personwaitinglist' => 'personne sur la liste d\'attente',
	'event_manager:peoplewaitinglist' => 'personnes sur la liste d\'attente',
	
	'event_manager:registration:list:navigation:waiting' => 'Liste d\'attente',
	'event_manager:registration:list:navigation:attending' => 'Participants',
	
	'event_manager:registration:view:savetopdf' => 'Enregistrer comme pdf',
	
	'event_manager:edit:title' => 'Créer / Modifier un évènement',
	'event_manager:edit:upload:title' => 'Ajouter un fichier à votre évènement',
	'event_manager:edit:form:tabs:profile' => 'Profile',
	'event_manager:edit:form:tabs:profile:toggle' => 'Vous pouvez définir des détails additionnels ici',
	'event_manager:edit:form:tabs:location' => 'Localisation',
	'event_manager:edit:form:tabs:location:toggle' => 'Vous pouvez fournir des informations complémentaires sur la localisation ici',
	'event_manager:edit:form:tabs:contact' => 'Informations de contact',
	'event_manager:edit:form:tabs:contact:toggle' => 'détaillez les informations de contact',
	'event_manager:edit:form:tabs:registration' => 'Inscription',
	'event_manager:edit:form:tabs:registration:toggle' => 'Détails spécifiques pour les inscriptions',
	'event_manager:edit:form:tabs:questions' => 'Registration Questions',
	'event_manager:edit:form:tabs:toggle' => 'Configure',
	'event_manager:edit:form:file' => 'Choisir un fichier',
	'event_manager:edit:form:files' => 'Fichier',
	'event_manager:edit:form:venue' => 'Lieu',
	'event_manager:edit:form:type' => 'Type',
	'event_manager:edit:form:location' => 'Adresse',
	'event_manager:edit:form:users:or' => 'ou',
	'event_manager:edit:form:users:add' => 'Ajouter un utilisateur',
	'event_manager:edit:form:organizer_guids' => 'Sélectionnez des membres organisateurs',
	'event_manager:edit:form:contact_guids' => 'Sélection des membres pour les contacts',
	'event_manager:event:edit:maps_address' => 'Adresse',
	'event_manager:edit:form:region' => 'Région',
	'event_manager:edit:form:contact_details' => 'Contact',
	'event_manager:edit:form:website' => 'site Web',
	'event_manager:edit:form:fee' => 'Prix',
	'event_manager:edit:form:fee_details' => 'Instruction de paiement',
	'event_manager:edit:form:options' => 'Options',
	'event_manager:edit:form:registration_options' => 'Options d\'inscription',
	'event_manager:edit:form:rsvp_options' => 'RSVP options',
	'event_manager:edit:form:shortdescription' => 'Courte Description',
	'event_manager:edit:form:shortdescription:help' => '',
	'event_manager:edit:form:description:help' => '',
	'event_manager:edit:form:tags:help' => '',
	'event_manager:edit:form:icon:help' => '',
	'event_manager:edit:form:type:help' => '',
	'event_manager:edit:form:comments_on:help' => '',
	'event_manager:edit:form:venue:help' => '',
	'event_manager:edit:form:location:help' => '',
	'event_manager:edit:form:region:help' => '',
	'event_manager:edit:form:organizer:help' => '',
	'event_manager:edit:form:contact_details:help' => '',
	'event_manager:edit:form:website:help' => '',
	'event_manager:edit:form:fee:help' => '',
	'event_manager:edit:form:fee_details:help' => '',
	'event_manager:edit:form:endregistration_day:help' => '',
	'event_manager:edit:form:max_attendees:help' => '',
	'event_manager:edit:form:organizer' => 'Organisateur',
	'event_manager:edit:form:with_program' => 'Cet événement à défini un programme journalier',
	'event_manager:edit:form:delete_current_icon' => 'Supprimer l\'icone actuelle ?',
	'event_manager:edit:form:comments_on' => 'Commentaires activés ?',
	'event_manager:edit:form:registration_ended' => 'Désactiver les inscriptions à cet événement',
	'event_manager:edit:form:registration_needed' => 'Les participants doivent spécifier un programme journalier et/ou répondre à des questions supplémentaires',
	'event_manager:edit:form:show_attendees' => 'Afficher les participants ?',
	'event_manager:edit:form:notify_onsignup' => 'Prévenez-moi lorsque quelqu\'un s\'inscrit',
	'event_manager:edit:form:start' => 'Début',
	'event_manager:edit:form:end' => 'Fin',
	'event_manager:edit:form:start_day' => 'Date',
	'event_manager:edit:form:start_day:from' => 'Date de début',
	'event_manager:edit:form:start_day:to' => 'Date de fin',
	'event_manager:edit:form:endregistration_day' => 'Date de fin des inscriptions',
	'event_manager:edit:form:start_time' => 'Heure de début',
	'event_manager:edit:form:end_time' => 'Heure de fin',
	'event_manager:edit:form:max_attendees' => 'Maximum de participants',
	'event_manager:edit:form:waiting_list' => 'Activer la liste d\'attente ?',
	'event_manager:edit:form:register_nologin' => 'Autoriser les utilisateurs non connectés à s\'inscrire ?',
	'event_manager:edit:form:spots_left' => 'Places disponibles',
	'event_manager:edit:form:spots_left:full' => 'L\'activité est complète',
	'event_manager:edit:form:spots_left:waiting_list' => ' Participant(e)s sur la liste d\'attente',
	'event_manager:edit:form:currenticon' => 'Icone actuelle',
	'event_manager:edit:form:icon' => 'Icone de l\'évènement (ne rien mettre pour laisser inchangé)',
	'event_manager:edit:form:registration_completed:toggle' => 'Cliquez ici pour paramétrer le texte de la page préinscription',
	'event_manager:edit:form:registration_completed' => 'Texte affiché une fois l\'inscription faite',
	'event_manager:edit:form:registration_completed:description' => 'Si vous ajoutez [NAME] le nom de l\'utilisateur inscrit sera affiché. Si vous ajoutez [EVENT] le nom de l\'événement sera affiché.',

	'event_manager:edit:form:slot_set' => 'Ensemble de Créneaux', // check the meaning
	'event_manager:edit:form:slot_set:add' => 'Ajout en ensemble de créneaux',
	'event_manager:edit:form:slot_set:empty' => 'Pas d\'ensemble de créneaux défini',
	'event_manager:edit:form:slot_set:description' => 'Ajouter un ensemble de créneaux permet de limiter les inscriptions pour un utilisateur à un seul créneaux par ensemble',

	'event_manager:form:program:day' => 'Ajouter un événement journalier',
	'event_manager:program:day:add' => 'Ajouter un jour',
	'event_manager:form:program:slot' => 'Ajouter un créneau d\'activité',
	'event_manager:program:slot:add' => 'Ajouter un créneau',
	
	'event_manager:editregistration:title' => 'Modifier le formulaire d\'inscription à un évènement',
	'event_manager:editregistration:addfield' => 'Ajouter un champ',
	'event_manager:editregistration:addfield:title' => 'Ajouter un champ d\'inscription',
	'event_manager:editregistration:fieldtype' => 'Type du champ',
	'event_manager:editregistration:fieldoptions' => 'Options du champ',
	'event_manager:editregistration:commasepatared' => 'Séparé par un virgule',
	'event_manager:editregistration:question' => 'Question',
	
	'event_manager:edit_program:title' => "Éditer le programme de l'événement",
	'event_manager:edit_program:description' => "Ici vous pouvez éditer/configurer le programme de l'événement.",
		
	'event_manager:registration:message:registrationnotneeded' => 'Inscription à cet évènement n\'est pas nécessaire.',
	'event_manager:registration:register:title' => 'S\'inscrire à l\'évènement',
	'event_manager:registration:registrationto' => 'S\'inscrire à ',
	'event_manager:registration:edit:title' => 'Modifier votre inscription',
	'event_manager:registration:edityourregistration' => 'Modifier votre inscription',
	'event_manager:registration:viewyourregistration' => 'Afficher votre inscription',
	'event_manager:registration:view:information' => 'Information',
	'event_manager:registration:yourregistration' => 'Votre inscription',
	'event_manager:registration:required_fields' => 'Veuillez remplir les champs obligatoires',
	'event_manager:registration:required_fields:info' => 'Les champs marqués avec * sont obligatoires',
	'event_manager:registration:slot_set:info' => 'Ce programme contient des ensembles de créneaux. Vous ne pouvez qu\'un de chaque ensemble.', // @fixme check the meaning

	'event_manager:event:registration:notification:owner:subject' => 'inscription évènement',
	'event_manager:event:registration:notification:user:subject' => 'inscription évènement',
	
	'event_manager:event:registration:notification:owner:summary:event_attending' => "%s s'est inscrit en tant que 'participant' à votre événement '%s'.",
	'event_manager:event:registration:notification:owner:text:event_attending' => 'Bonjour %s,

%s c\'est inscrit(e) comme \'participant(e)\' à l\'évènement \'%s\'.',
	
	'event_manager:event:registration:notification:user:summary:event_attending' => "Vous vous êtes bien inscrits en tant que 'participant' à l'événement '%s'.",
	'event_manager:event:registration:notification:user:text:event_attending' => 'Bonjour %s,

Vous êtes maintenant inscrit(e) comme \'participant(e)\' l\'évènement \'%s\'.',
	
	'event_manager:event:registration:notification:owner:summary:event_waitinglist' => "%s est sur la liste d'attente de votre événement '%s'.",
	'event_manager:event:registration:notification:owner:text:event_waitinglist' => 'Bonjour %s,

%s est sur la liste d\'attente pour votre évènement \'%s\' .',
	
	'event_manager:event:registration:notification:user:summary:event_waitinglist' => "Vous être maintenant sur la liste d'attente de l'événement '%s'.",
	'event_manager:event:registration:notification:user:text:event_waitinglist' => 'Bonjour %s,

Vous êtes en liste d\'attente pour l\'évènement \'%s\'.',
	
	'event_manager:event:registration:notification:owner:summary:event_exhibiting' => "%s s'est inscrit en tant que 'exposant' à votre événement '%s'.",
	'event_manager:event:registration:notification:owner:text:event_exhibiting' => 'Bonjour %s,

%s s\'est inscrit(e) comme \'exposant(e)\' à votre évènement \'%s\' .',
	
	'event_manager:event:registration:notification:user:summary:event_exhibiting' => "Vous vous êtes bien inscrits en tant que 'exposant' pour l'événement '%s'.",
	'event_manager:event:registration:notification:user:text:event_exhibiting' => 'Bonjour %s,

Vous êtes maintenant inscrit(e) comme \'exposant(e)\' à l\'évènement \'%s\' .',
	
	'event_manager:event:registration:notification:owner:summary:event_organizing' => "%s s'est inscrit comme 'organisateur' à votre événement '%s'.",
	'event_manager:event:registration:notification:owner:text:event_organizing' => 'Bonjour %s,

%s s\'est inscrit(e) comme \'organisateur/organisatrice\' à votre évènement \'%s\' .',
	
	'event_manager:event:registration:notification:user:summary:event_organizing' => "Vous vous êtes bien inscrits en tant que 'Organisateur' pour l'événement '%s'.",
	'event_manager:event:registration:notification:user:text:event_organizing' => 'Bonjour %s,

Vous êtes maintenant inscrit(e) comme \'organisateur/organisatrice\' à l\'évènement \'%s\' .',
	
	'event_manager:event:registration:notification:owner:summary:event_presenting' => "%s s'est inscrit en tant que 'orateur' à votre événement '%s'.",
	'event_manager:event:registration:notification:owner:text:event_presenting' => 'Bonjour %s,

%s s\'est inscrit(e) comme \'animateur/animatrice\' à votre évènement \'%s\' .',
	
	'event_manager:event:registration:notification:user:summary:event_presenting' => "Vous vous êtes bien inscrits en tant que 'orateur' lors de l'événement '%s'.",
	'event_manager:event:registration:notification:user:text:event_presenting' => 'Bonjour %s,

Vous êtes maintenant inscrit(e) comme \'animateur/animatrice\' à l\'évènement \'%s\' .',
	
	'event_manager:event:registration:notification:owner:summary:event_interested' => "%s s'est inscrit comme 'intéressé' pour votre événement '%s'.",
	'event_manager:event:registration:notification:owner:text:event_interested' => 'Bonjour %s,

%s s\'est inscrit(e) comme \'intéressé(e)\' à votre évènement \'%s\' .',
	
	'event_manager:event:registration:notification:user:summary:event_interested' => "Vous vous êtes bien inscrits en tant que 'intéressé' pour l'événement '%s'.",
	'event_manager:event:registration:notification:user:text:event_interested' => 'Bonjour %s,

Vous êtes maintenant inscrit(e) comme \'intéressé(e)\' à l\'évènement \'%s\' .',
	
	'event_manager:event:registration:notification:owner:summary:event_undo' => "%s s'est désinscrit de votre événement '%s'.",
	'event_manager:event:registration:notification:owner:text:event_undo' => 'Bonjour %s,

%s s\'est désinscrit(e) de votre évènement \'%s\'.',
	
	'event_manager:event:registration:notification:user:summary:event_undo' => "Vous vous êtes bien désinscrits de l'événement '%s'.",
	'event_manager:event:registration:notification:user:text:event_undo' => 'Bonjour %s,

Vous êtes maintenant désinscrit(e) de l\'évènement \'%s\' .',
	
	'event_manager:event:registration:notification:user:summary:event_spotfree' => "Quelqu'un s'est désinscrit de l'événement '%s' (%s). Vous êtes monté dans la liste d'attente.",
	'event_manager:event:registration:notification:user:text:event_spotfree' => 'Bonjour %s,

une personne s\'est désinscrite de l\'évènement \'%s\' . Vous n\'êtes plus sur la liste d\'attente.',
	
	'event_manager:event:registration:notification:program:linktext' => 'Pour voir le programme cliquez sur ce lien',
	'event_manager:event:registration:notification:unsubscribe:linktext' => 'Si vous ne voulez plus participé à cet événement, utilisé ce lien',
	
	'event_manager:event:rsvp' => 'Je souhaite',
	'event_manager:event:location:plan_route' => 'Planifier mon itinéraire',
	'event_manager:event:uploadfiles' => 'Upload des fichiers',
	'event_manager:event:uploadfiles:no_files' => 'Utilisé le bouton plus pour ajouter des fichiers à cet événement',
	'event_manager:event:attendees' => 'Participant(e)s',
	'event_manager:event:program' => 'Programme',
	'event_manager:event:editquestions' => 'Modifier les questions pour s\'inscrire',
	'event_manager:event:waitinglist:empty' => 'La liste d\'attente est vide',
	'event_manager:event:exportattendees' => 'Exporter les participant(e)s',
	'event_manager:event:search_attendees' => 'Rechercher des participants',
	'event_manager:event:initial:day:title' => 'Événement jour 1',
	'event_manager:event:initial:slot:title' => 'Titre de l\'activité',
	'event_manager:event:initial:slot:description' => 'Description de l\'activité',

	'event_manager:registrationform:editquestion:required' => 'Obligatoire ?',
	'event_manager:registrationform:editquestion:text:placeholder' => 'Entrez une question que vous voulez poser.',
	
	'event_manager:event:file:notfound:text' => 'Le fichier que vous cherchez n\'a pas été trouvé',
	
	'event_manager:event:register:register_link' => 'S\'inscrire à cet évènement',
	'event_manager:event:register:log_in_first' => 'Connectez vous pour vous inscrire à cet événement',

	'event_manager:event:menu:user_hover:resend_confirmation' => 'Renvoyer le courriel de confirmation',
	'event_manager:event:menu:user_hover:move_to_attendees' => 'Mettre dans les participants',

	'event_manager:event:menu:title:add_to_calendar' => 'Ajouter au calendrier',
	
	'event_manager:event:view:event' => 'Evènements',
	'event_manager:event:view:date' => 'Date',
	'event_manager:event:view:createdby' => 'Créé par',
	'event_manager:event:view:contact_persons' => 'Contact',
	
	// relationships
	'event_manager:event:relationship:event_attending' => 'Participation',
	'event_manager:event:relationship:event_attending:entity_menu' => '%s participants',
	'event_manager:event:relationship:event_attending:label' => 'Participants',
	'event_manager:event:relationship:event_waitinglist' => 'Liste d\'attente',
	'event_manager:event:relationship:event_waitinglist:label' => 'Sur la liste d\'attente',
	'event_manager:event:relationship:event_pending:label' => 'En attente du courriel de validation',
	'event_manager:event:relationship:event_interested' => 'seulement intéressé(e)',
	'event_manager:event:relationship:event_interested:label' => 'Intéressé',
	'event_manager:event:relationship:event_presenting' => 'Animation',
	'event_manager:event:relationship:event_presenting:label' => 'Orateurs',
	'event_manager:event:relationship:event_exhibiting' => 'Exposition',
	'event_manager:event:relationship:event_exhibiting:label' => 'Exposants',
	'event_manager:event:relationship:event_organizing' => 'Organisation',
	'event_manager:event:relationship:event_organizing:label' => 'Organisateurs',
	'event_manager:event:relationship:undo' => 'annuler',
	'event_manager:event:relationship:kick' => 'Exclure de l\'évènement',
	
	'event_manager:event:rsvp:registration_ended' => 'Les inscriptions à cet évènement sont closes.',
	'event_manager:event:rsvp:waiting_list' => 'Liste d\'attente',
	'event_manager:event:rsvp:nospotsleft' => 'Cet évènement n\'a pas de places disponibles.',
	'event_manager:event:rsvp:waiting_list:message' => 'L\'évènement auquel vous souhaitez participer est complet. Remplissez le formulaire pour être mis en liste d\'attente.',
	
	'event_manager:event:relationship:message:event_attending' => 'Vous participez à cet évènement',
	'event_manager:event:relationship:message:event_waitinglist' => 'Vous êtes en liste d\'attente pour cet évènement',
	'event_manager:event:relationship:message:event_interested' => 'Vous êtes intéressé(e) par cet évènement',
	'event_manager:event:relationship:message:event_presenting' => 'Vous êtes animateur/animatrice de cet évènement',
	'event_manager:event:relationship:message:event_exhibiting' => 'Vous êtes exposant à cet évènement',
	'event_manager:event:relationship:message:event_organizing' => 'Vous êtes organisateur/organisatrice pour cet évènement',
	'event_manager:event:relationship:message:event_undo' => 'Vous quittez cet évènement',
	'event_manager:event:relationship:message:error' => 'Erreur pour rejoindre/quitter l\'évènement',
	'event_manager:event:relationship:message:unavailable_relation' => "Le type de RSVP que vous avez choisit n'est pas disponible",
	
	// widgets
	'event_manager:widgets:events:title' => 'évènements manager',
	'event_manager:widgets:events:description' => 'Afficher les évènements à venir',
	
	'event_manager:widgets:events:numbertodisplay' => 'Nombre d\'évènements à afficher',
	'event_manager:widgets:events:showevents' => 'Afficher les évènements',
	'event_manager:widgets:events:showevents:icreated' => 'que j\'ai créé',
	'event_manager:widgets:events:showevents:attendingto' => 'auxquels je participe',
	'event_manager:widgets:events:group' => "Entrer le nom d'un groupe pour limité le résultat (optionnelle)",
	'event_manager:widgets:events:group_guid' => "Entrer le guid d'un groupe pour limité le résultat (optionnelle)",

	'event_manager:widgets:highlighted_events:title' => 'Événements mis en avant',
	'event_manager:widgets:highlighted_events:description' => 'Sélectionnez l\'événement que vous voulez mettre en avant dans ce widget',
	'event_manager:widgets:highlighted_events:edit:event_guids' => 'Sélectionnez les événements',
	'event_manager:widgets:highlighted_events:edit:show_past_events' => 'Montrer les événements passés',
	
	// actions
	'event_manager:action:event:edit:ok' => 'évènements enregistré',
	'event_manager:action:event:edit:error_fields' => 'Veuillez remplir les champs obligatoires',
	'event_manager:action:event:edit:end_before_start' => "L'heure de fin doit être après l'heure de début.",
	'event_manager:action:registration:edit:error_fields_with_program' => 'Remplissez les champs obligatoire et sélectionnez au moins un emplacement d\'activité libre pour y assister',
	'event_manager:action:registration:edit:error_fields_program_only' => 'Sélectionnez au moins un emplacement d\'activité libre pour y assister',
	'event_manager:action:registration:edit:error_slots' => "Vous ne pouvez choisir qu'un seul créneau par ensemble. Plus d'un ont été choisit pour l'ensemble %s.", // @FIXME check the meaning
	'event_manager:action:register:email:account_exists:attending' => "Il y a déjà un compte avec cette adresse courriel qui est enregistré en tant que participant pour cette événement.",
	'event_manager:action:register:email:account_exists:waiting' => "Il y a déjà un compte avec cette adresse courriel sur la liste d'attente de cet événement.",
	'event_manager:action:register:email:account_exists:pending' => "Il y a déjà une inscription pour cette adresse courriel dans la liste d'attente en attente de confirmation. Nous vous avons renvoyer le courriel de confirmation.",
	'event_manager:action:register:pending' => "Votre inscription est presque terminé. Pour confirmer votre inscription dans le courriel que nous vous avons envoyé.",
	'event_manager:action:resend_confirmation:success' => "Le courriel de confirmation à été renvoyé",
	'event_manager:action:move_to_attendees:success' => "L'utilisateur à été retirer des participants",
	'event_manager:action:slot:day_not_found' => "Le jour demandé n'as pas été trouvé",
	'event_manager:action:slot:missing_fields' => "Veuillez remplir les champs requis",
	'event_manager:action:slot:not_found' => "Le créneau n'as pas été trouvé",	
	'event_manager:action:slot:cannot_save' => "Une erreur est survenu pendant l'enregistrement du créneaux",
	'event_manager:action:slot:saved' => 'Créneau enregistré',
	'event_manager:action:slot:delete:error' => 'Le créneau ne peut être supprimé',
	
	// settings
	'event_manager:settings:google_maps' => 'Réglages Google Maps',
	'event_manager:settings:google_api_key' => 'Entrez votre Google API key',
	'event_manager:settings:google_maps:enterdefaultlocation' => 'Entrez un emplacement par défaut que google maps utilisera pour se centrer dessus',
	'event_manager:settings:google_maps:enterdefaultzoom' => 'Entrez un niveau de zoom par défaut pour google maps (0 = zoom +, 19 = zoom -)',
	'event_manager:settings:google_api_key:clickhere' => 'Allez à <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key">https://developers.google.com/maps/documentation/javascript/tutorial#api_key</a> pour obtenir votre "Google API key"',
	'event_manager:settings:other' => 'Autre',
	'event_manager:settings:region_list' => 'Entrez (séparé par une virgule) les régions de l\'évènements',
	'event_manager:settings:type_list' => 'Entrez (séparé par une virgule) les types d\'évènements',
	'event_manager:settings:notification_sender' => 'Envoi d\'une notification RSVP (email)',
	
	'event_manager:settings:migration:site:whocancreate' => 'Qui peut créer des évènements',
	'event_manager:settings:migration:site:whocancreate:admin_only' => 'administrateur seulement',
	'event_manager:settings:migration:site:whocancreate:everyone' => 'tout le monde',
	
	'event_manager:settings:migration:group:whocancreate' => 'Qui peut créer un évènement de groupe',
	'event_manager:settings:migration:group:whocancreate:group_admin' => 'propriétaires du groupe et administrateurs seulement',
	'event_manager:settings:migration:group:whocancreate:members' => 'N\'importe quel membre du groupe',
	'event_manager:settings:migration:group:whocancreate:no_one' => 'personne',
	
	// unsubscribe from event
	'event_manager:unsubscribe:title' => "Désinscription depuis: %s",
	'event_manager:unsubscribe:description' => "Si vous voulez vous désinscrire de l'événement %s vous pouvez entrer votre adresse courriel ci-dessous. Si le courriel lié à l'inscription est trouvé, vous recevrez un mail avec des instructions à suivre.",

	'event_manager:action:unsubscribe:error:no_registration' => "Pas d'inscription trouvé pour le courriel fourni",
	'event_manager:action:unsubscribe:error:mail' => "Une erreur est survenu pendant l'envoi du courriel de confirmation, veuillez réessayer",
	'event_manager:action:unsubscribe:success' => "Un courriel vous à été envoyer pour confirmé votre désinscription à cet événement. Vérifier votre boite et suivez les instructions du courriel",

	'event_manager:unsubscribe:confirm:subject' => "Confirmer votre désinscription de %s",
	'event_manager:unsubscribe:confirm:message' => "Bonjour %s,

Vous avez demander à vous désinscrire de l'événement %s (%s). Si vous êtes bien à l'origine de cette demande, veuillez cliquer sur le lien ci-dessous pour terminé la demande.

%s

Sinon ne tenez pas compte de ce courriel.",

	'event_manager:unsubscribe_confirm:error:code' => "Le code fourni est incorrect, veuillez vérifier votre courriel",
	'event_manager:unsubscribe_confirm:title' => "Confirmer la désinscription de: %s",
	'event_manager:unsubscribe_confirm:description' => "%s vous avez demandé à vous désinscrire de %s. Pour valider la désinscription, veuillez cliquer sur le bouton de confirmation ci-dessous.",

	'event_manager:action:unsubscribe_confirm:error' => "Une erreur est survenu pendant la description, veuillez réessayer",
	'event_manager:action:unsubscribe_confirm:success' => "Vous vous êtes bien désinscrits à cet événement",

	// registration completed
	'event_manager:registration:completed:title' => "Merci pour votre inscription pour %s",
	'event_manager:registration:completed' => "%s vous avez terminé votre inscription pour %s. Nous espérons que vous apprécierez l'événement.",
	'event_manager:registration:continue' => "Continuer vers l'événement",

	// registration confirm
	'event_manager:registration:confirm:error:code' => "Code de validation invalide, vérifier votre courriel pour le code correct",
	'event_manager:registration:confirm:title' => "Confirmez votre inscription pour %s",
	'event_manager:registration:confirm:description' => "Bonjour %s,

Pour confirmer votre inscription pour l'événement \"%s\", veuillez cliquez sure le bouton ci-dessous.

Si vous n'avez pas demander à vous inscrire ou pour retirer votre participation; cliquez sur le bouton supprimer.",
	'event_manager:registration:confirm:delete' => "Est vous sûr de vouloir supprimer votre inscription ?",

	'event_manager:registration:confirm:subject' => "Veuillez confirmer votre inscription pour %s",
	'event_manager:registration:confirm:message' => "Bonjour %s,

Pour terminer votre inscription pour l'événement \"%s\", veuillez cliquer sur ce lien 
%s

Si vous ne vous êtes pas inscrit à cet événement, vous pouvez ignorer ce courriel ou cliquer sur le lien et supprimer votre inscription.",

	// river
	'event_manager:river:event_relationship:create:event_attending' => '%s participe à %s',
	'event_manager:river:event_relationship:create:event_interested' => '%s est intéressé(e) par %s',
	'event_manager:river:event_relationship:create:event_presenting' => '%s anime à %s',
	'event_manager:river:event_relationship:create:event_exhibiting' => '%s expose à %s',
	'event_manager:river:event_relationship:create:event_organizing' => '%s organise l\'évènement %s',
	
	'river:create:object:event' => '%s à créé l\'événement %s',
	
	'event_manager:date:to' => 'Vers',
	'requiredfields' => 'Champs obligatoires',	
	'confirm' => 'Confirmer',

	'event_manager:addevent:mail:title' => 'Exporter vers',
	'event_manager:addevent:mail:service:appleical' => 'Apple',
	'event_manager:addevent:mail:service:google' => 'Google',
	'event_manager:addevent:mail:service:outlook' => 'Outlook',
	'event_manager:addevent:mail:service:outlookcom' => 'Outlook.com',
	'event_manager:addevent:mail:service:yahoo' => 'Yahoo',
);

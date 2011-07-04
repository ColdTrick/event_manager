<?php 

	class EventDay extends ElggObject 
	{
		const SUBTYPE = "eventday";
		
		protected function initialise_attributes() 
		{
			global $CONFIG;
			parent::initialise_attributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
		
		public function getEventSlots()
		{
			global $CONFIG;
			
			$entities_options = array(
				'type' => 'object',
				'subtype' => 'eventslot',
				'relationship_guid' => $this->getGUID(),
				'relationship' => 'event_day_slot_relation',
				'inverse_relationship' => true,
				'full_view' => false,
				'joins' => array(
					"JOIN {$CONFIG->dbprefix}metadata n_table on e.guid = n_table.entity_guid",
					"JOIN {$CONFIG->dbprefix}metastrings msn on n_table.name_id = msn.id",
					"JOIN {$CONFIG->dbprefix}metastrings msv on n_table.value_id = msv.id"),
				'wheres' => array("(msn.string IN ('start_time'))"),
				'order_by' => "msv.string {$order}",
				'limit' => false,
					
			);
		 
			return elgg_get_entities_from_relationship($entities_options);
		}
	}
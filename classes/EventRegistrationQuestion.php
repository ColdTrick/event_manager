<?php
/**
 * EventRegistrationQuestion
 *
 * @package EventManager
 *
 */
class EventRegistrationQuestion extends ElggObject {
	const SUBTYPE = 'eventregistrationquestion';

	/**
	 * initializes the default class attributes
	 *
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Returns the answer given by a user
	 *
	 * @param string $user_guid guid of the entity
	 *
	 * @return boolean|ElggAnnotation
	 */
	public function getAnswerFromUser($user_guid = null) {
		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		$params = [
			'guid' => $this->getGUID(),
			'annotation_name' => 'answer_to_event_registration',
			'annotation_owner_guid' => $user_guid,
			'limit' => 1
		];

		$annotations = elgg_get_annotations($params);
		if (empty($annotations)) {
			return false;
		}
		
		return $annotations[0];
	}

	/**
	 * Removes the answer given by a user
	 *
	 * @param string $user_guid guid of the entity
	 *
	 * @return void
	 */
	public function deleteAnswerFromUser($user_guid = null) {
		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		$annotation = $this->getAnswerFromUser($user_guid);
		if ($annotation) {
			$annotation->delete();
		}
	}

	/**
	 * Updates the answer given by a user
	 *
	 * @param Event  $event      the event entity used for setting the access of the annotation correctly
	 * @param string $new_answer the new answer
	 * @param string $user_guid  guid of the entity giving the answer
	 *
	 * @return void
	 */
	public function updateAnswerFromUser($event, $new_answer, $user_guid = null) {
		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		$old_answer = $this->getAnswerFromUser($user_guid);
		if ($old_answer && get_user($user_guid)) {
			if (!empty($new_answer)) {
				update_annotation($old_answer->id, 'answer_to_event_registration', $new_answer, '', $user_guid, $event->access_id);
			} else {
				elgg_delete_annotation_by_id($old_answer->id);
			}
		} else {
			$this->annotate('answer_to_event_registration', $new_answer, $event->access_id, $user_guid);
		}
	}

	/**
	 * Returns the options of this question
	 *
	 * @return array
	 */
	public function getOptions() {
		$field_options = [];

		if (!empty($this->fieldoptions)) {
			$field_options = string_to_tag_array($this->fieldoptions);
			$field_options = array_combine(array_values($field_options), $field_options); // input radio and checkbox require non-numeric keys
		}

		return $field_options;
	}
}

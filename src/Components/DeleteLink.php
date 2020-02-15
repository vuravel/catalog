<?php

namespace Vuravel\Catalog\Components;

use Vuravel\Catalog\Component;
use Vuravel\Form\Traits\{UsableInForms, TriggerStyles};

class DeleteLink extends Component
{
    use UsableInForms, TriggerStyles;
    
    public $component = 'DeleteLink';

    const DB_DELETE_ROUTE = 'vuravel-catalog.db.delete';

    public $data = [
    	'deleteTitle' => 'Delete this item',
    	'confirmMessage' => 'Are you sure?',
    	'cancelMessage' => 'Cancel'
    ];

	public function __construct($item, $label = '')
	{
		parent::__construct($label);

		if(!$label) //just an icon
			$this->icon('icon-trash');

		$this->post(self::DB_DELETE_ROUTE, [
				'id' => method_exists($item, 'getKey') ? $item->getKey() : $item->id,
				'objectClass' => get_class($item)
			])
			->emitsDirectOnSuccess('deleted');

		$this->deleteTitle(__($this->data('deleteTitle')));
		$this->confirmMessage(__($this->data('confirmMessage')));
		$this->cancelMessage(__($this->data('cancelMessage')));

	}

	/**
	 * Sets the title of the modal that opens when the DeleteLink is clicked.
	 * By default, it is 'Delete this item'.
	 *
	 * @param      string  $deleteTitle  The title of the action. 
	 *
	 * @return     self  
	 */
	public function deleteTitle($deleteTitle)
	{
		$this->data([
			'deleteTitle' => $deleteTitle
		]);
		return $this;
	}

	/**
	 * Sets the label (confirmation message) of the button that will really perform the delete request.
	 * By default, it is 'Are you sure?'.
	 *
	 * @param      string  $confirmMessage  The label of the confirmation button. 
	 *
	 * @return     self  
	 */
	public function confirmMessage($confirmMessage)
	{
		$this->data([
			'confirmMessage' => $confirmMessage
		]);
		return $this;
	}

	/**
	 * Sets the label (cancellation message) of the button that will close the modal.
	 * By default, it is 'Cancel'.
	 *
	 * @param      string  $cancelMessage  The label of the cancel button. 
	 *
	 * @return     self  
	 */
	public function cancelMessage($cancelMessage)
	{
		$this->data([
			'cancelMessage' => $cancelMessage
		]);
		return $this;
	}

}
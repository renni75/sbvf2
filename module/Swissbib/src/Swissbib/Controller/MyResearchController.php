<?php
namespace Swissbib\Controller;

use VuFind\Controller\MyResearchController as VFMyResearchController;
use Swissbib\VuFind\ILS\Driver\Aleph;
use Zend\View\Model\ViewModel;


class MyResearchController extends VFMyResearchController {

	/**
	 * Show photo copy requests
	 *
	 * @return	ViewModel
	 */
	public function photocopiesAction() {
		// Stop now if the user does not have valid catalog credentials available:
		if( !is_array($patron = $this->catalogLogin()) ) {
			return $patron;
		}

		/** @var Aleph $catalog  */
		$catalog = $this->getILS();

		// Get photo copies details:
		$photoCopies = $catalog->getPhotocopies($patron['id']);

		return $this->createViewModel(array('photoCopies' => $photoCopies));
	}



	/**
	 * Get bookings
	 *
	 * @return	ViewModel
	 */
	public function bookingsAction() {
				// Stop now if the user does not have valid catalog credentials available:
		if( !is_array($patron = $this->catalogLogin()) ) {
			return $patron;
		}

		/** @var Aleph $catalog  */
		$catalog = $this->getILS();

		// Get photo copies details:
		$bookings = $catalog->getBookings($patron['id']);

		return $this->createViewModel(array('bookings' => $bookings));
	}



	/**
	 * Get location parameter from route
	 *
	 * @return	String|Boolean
	 */
	protected function getLocationFromRoute() {
		return $this->params()->fromRoute('location', false);
	}



	/**
	 * Inject location from route
	 *
	 * @inheritDoc
	 */
	protected function createViewModel($params = null) {
		$viewModel	= parent::createViewModel($params);

		$viewModel->location = $this->getLocationFromRoute() ?: 'baselbern';

		return $viewModel;
	}








    /**
     * (local) Search User Settings
     *
     * @return mixed
     */
    public function searchsettingsAction()
    {
        $view   = parent::profileAction();

        /** @var $user  \VuFind\Db\Row\User */
        $user = $this->getUser();
        if( is_object($user) && get_class($user) === 'VuFind\Db\Row\User' ) {
            $userData   = $user->toArray();
            $nickname   = 'test'; //$userData['sb_nickname'];
        } else {
            $nickname   = '';
        }

        $view->nickname = $nickname;
        $view->optsLanguage = $this->getOptionsLanguage();
        $view->optsMaxHits  = $this->getOptionsMaximumHits();


        //...Prove of concept...
        //@todo implement actual values storing
/** @var $userLocalData \Swissbib\Db\Table\UserLocalData */
$userLocalData  = $this->getServiceLocator()->get('Swissbib\DbTablePluginManager')->get('userlocaldata');
$userLocalData->createOrUpdateLanguage('duetsch', 1);

        return $view;
    }



    /**
     * Get key-label tupels of languages.
     * Labels are each in the resp. language, not to be localized.
     *
     * @return  Array
     */
    public function getOptionsLanguage() {
        return array(
            'de'    => 'Deutsch',
            'en'    => 'English',
            'fr'    => 'Francais',
            'it'    => 'Italiano'
        );
    }



    /**
     * @return array
     */
    public function getOptionsMaximumHits() {
        return array(
            10,
            50,
            250,
            1250,
            5000
        );
    }


    /**
     * EXPLORATION (prove of concept)
     * Store user data sb_nickname to local VF database
     *
     * @return  mixed
     */
    protected function saveaccountlocalAction() {
        $view = $this->createViewModel();
        $view->setTerminal(true);

        $this->layout()->setTemplate('myresearch/profile');

        /** @var $user  \VuFind\Db\Row\User */
        $user = $this->getUser();
        if( is_object($user) && get_class($user) === 'VuFind\Db\Row\User' ) {
            $nickname   = array_key_exists('nickname', $_GET) ? $_GET['nickname'] : '';
            $user->sb_nickname  = $nickname;
            $user->save();

            $this->layout()->nickname   = $nickname;
        } else {
            $this->layout()->nickname   = '';
        }

        return parent::profileAction();
    }

}
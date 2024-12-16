<?php
namespace Yanah\LaravelKwik\Traits;

use Yanah\LaravelKwik\App\Contracts\PageAffixInterface;

trait PageWrapperTrait
{
    private $crudPage;


    public function assignPageSetup($page)
    {
        $this->crudPage = $page;
    }

    /**
     * Retrieves the appended file for the current CRUD page.
     *
     * @return string|null
     * @throws \Exception
     */
    public function wrapperControl()
    {
        try {

            if($this->crudPage instanceof PageAffixInterface)
            {
                return $this->crudPage->definePages();
            }

        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return null;
    }
}
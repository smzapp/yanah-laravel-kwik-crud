<?php
namespace Yanah\LaravelKwik\Traits;

use Yanah\LaravelKwik\App\Contracts\PageAffixInterface;
use Illuminate\Http\Request;

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
            if($this->crudPage instanceof PageAffixInterface) {
                return $this->crudPage->definePages();
            }
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return null;
    }

    public function getStorePayload(Request $request, $childCreateForm)
    {
        $model = $this->getModelInstance();
        $validations = $childCreateForm->getValidationRules();
        
        if(is_array($validations) && empty($validations)) {
            $payload = $request->all();
        } else {
            $validatedData = $request->validate($validations);
            
            $payload = array_merge(
                $validatedData, 
                array_intersect_key($request->only($model->getFillable()), array_flip($model->getFillable())) 
            );
        } 

        return $payload;
    }

}
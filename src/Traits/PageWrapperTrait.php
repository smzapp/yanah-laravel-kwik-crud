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

    public function getFilteredPayload(Request $request, $childForm)
    {
        $model = $this->getModelInstance();
        $validations = $childForm->getValidationRules();
        
        if(is_array($validations) && empty($validations)) {
            $payload = $request->all();
        } else {
            $validatedData = $request->validate($validations);
            
            if ($this->shouldIncludeFillable)
            $payload = array_merge(
                $validatedData, 
                array_intersect_key($request->only($model->getFillable()), array_flip($model->getFillable())) 
            );
        } 

        return $payload;
    }
}
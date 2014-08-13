<?php namespace Kohkimakimoto\MessageBinder\View;

use Illuminate\View\View as IlluminateView;
use Illuminate\Support\MessageBag;

class View extends IlluminateView {

    /**
     * Add validation errors to the view.
     *
     * @param  \Illuminate\Support\Contracts\MessageProviderInterface|array  $provider
     * @return $this
     */
    public function withMessages($provider)
    {
        if ($provider instanceof MessageProviderInterface)
        {
            $this->with('messages', $provider->getMessageBag());
        }
        else
        {
            $this->with('messages', new MessageBag((array) $provider));
        }

        return $this;
    }

}

<?php namespace Kohkimakimoto\MessageBinder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Kohkimakimoto\MessageBinder\View\Factory;

class MessageBinderServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('kohkimakimoto/message-binder');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->overrideViewFactory();
		$this->registerSessionBinder();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

	protected function overrideViewFactory()
	{
		$this->app->bindShared('view', function($app)
		{

			// Next we need to grab the engine resolver instance that will be used by the
			// environment. The resolver will be used by an environment to get each of
			// the various engine implementations such as plain PHP or Blade engine.
			$resolver = $app['view.engine.resolver'];

			$finder = $app['view.finder'];

			$env = new Factory($resolver, $finder, $app['events']);

			// We will also set the container instance on this view environment since the
			// view composers may be classes registered in the container, which allows
			// for great testable, flexible composers for the application developer.
			$env->setContainer($app);

			$env->share('app', $app);

			return $env;
		});
	}

	/**
	 * Register the session binder for the view environment.
	 *
	 * @return void
	 */
	protected function registerSessionBinder()
	{
		list($app, $me) = array($this->app, $this);

		$app->booted(function() use ($app, $me)
		{
			// If the current session has an "messages" variable bound to it, we will share
			// its value with all view instances so the views can easily access messages
			// without having to bind. An empty bag is set when there aren't messages.
			if ($me->sessionHasMessages($app))
			{
				$messages = $app['session.store']->get('messages');
				if ($messages instanceof MessageProviderInterface) {
					$messages = $messages->getMessageBag();
				} else {
					$messages = new MessageBag((array)$messages);
				}

				$app['view']->share('messages', $messages);
			}
			// Putting the messages in the view for every view allows the developer to just
			// assume that some messages are always available, which is convenient since
			// they don't have to continually run checks for the presence of messages.
			else
			{
				$app['view']->share('messages', new MessageBag);
			}

		});
	}

	/**
	 * Determine if the application session has errors.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return bool
	 */
	public function sessionHasMessages($app)
	{
		$config = $app['config']['session'];

		if (isset($app['session.store']) && ! is_null($config['driver']))
		{
			return $app['session.store']->has('messages');
		}
	}
}

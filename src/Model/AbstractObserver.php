<?php

namespace Ronanchilvers\Db\Model;

use Ronanchilvers\Db\Model;

/**
 * Abstract observer that defines all hooks as empty
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class AbstractObserver implements ObserverInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function loaded(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function creating(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function created(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function updating(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function updated(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function saving(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function saved(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function deleting(Model $model)
    {}

    /**
     * {@inheritdoc}
     *
     * @param \Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function deleted(Model $model)
    {}
}

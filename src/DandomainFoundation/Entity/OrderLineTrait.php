<?php

namespace Loevgaard\DandomainStock\DandomainFoundation\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Loevgaard\DandomainStock\Entity\StockMovement;
use Loevgaard\DandomainStock\Exception\StockMovementProductMismatchException;

trait OrderLineTrait
{
    /**
     * @var StockMovement[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Loevgaard\DandomainStock\Entity\StockMovement", mappedBy="orderLine", cascade={"persist"})
     */
    protected $stockMovements;

    /**
     * @param \Loevgaard\DandomainStock\Entity\StockMovement $stockMovement
     *
     * @return OrderLineTrait
     *
     * @throws \Loevgaard\DandomainStock\Exception\StockMovementProductMismatchException
     */
    public function addStockMovement(StockMovement $stockMovement)
    {
        $this->initStockMovements();

        if ($this->stockMovements->count()) {
            /** @var StockMovement $firstStockMovement */
            $firstStockMovement = $this->stockMovements->first();
            if ($stockMovement->getProduct()->getId() !== $firstStockMovement->getProduct()->getId()) {
                throw new StockMovementProductMismatchException('The product id of the first product is `'.$firstStockMovement->getProduct()->getId().'` while the one you are adding has this id: `'.$stockMovement->getProduct()->getId().'`');
            }
        }

        if (!$this->stockMovements->contains($stockMovement)) {
            $this->stockMovements->add($stockMovement);
        }

        return $this;
    }

    /**
     * @return \Loevgaard\DandomainStock\Entity\StockMovement[]
     */
    public function getStockMovements()
    {
        $this->initStockMovements();

        return $this->stockMovements;
    }

    /**
     * @param \Loevgaard\DandomainStock\Entity\StockMovement $stockMovements
     *
     * @return OrderLineTrait
     *
     * @throws \Loevgaard\DandomainStock\Exception\StockMovementProductMismatchException
     */
    public function setStockMovements(StockMovement $stockMovements)
    {
        foreach ($stockMovements as $stockMovement) {
            $this->addStockMovement($stockMovement);
        }

        return $this;
    }

    /**
     * Say you have these two stock movements associated with this order line:.
     *
     * | qty | product |
     * -----------------
     * | 1   | Jeans   |
     * | -1  | Jeans   |
     *
     * Then the effective stock movement would be
     *
     * | qty | product |
     * -----------------
     * | 0   | Jeans   |
     *
     * And this is what we return in this method
     *
     * Returns null if the order line has 0 stock movements
     *
     * @return \Loevgaard\DandomainStock\Entity\StockMovement|null
     *
     * @throws \Loevgaard\DandomainStock\Exception\CurrencyMismatchException
     * @throws \Loevgaard\DandomainStock\Exception\UnsetCurrencyException
     */
    public function computeEffectiveStockMovement(): ?StockMovement
    {
        $this->initStockMovements();

        if (!$this->stockMovements->count()) {
            return null;
        }

        /** @var StockMovement $lastStockMovement */
        $lastStockMovement = $this->stockMovements->last();

        $qty = 0;
        $stockMovement = $lastStockMovement->copy();

        foreach ($this->stockMovements as $stockMovement) {
            $qty += $stockMovement->getQuantity();
        }

        $stockMovement->setQuantity($qty);

        return $stockMovement;
    }

    protected function initStockMovements(): void
    {
        if (!is_null($this->stockMovements)) {
            return;
        }

        $this->stockMovements = new ArrayCollection();
    }
}

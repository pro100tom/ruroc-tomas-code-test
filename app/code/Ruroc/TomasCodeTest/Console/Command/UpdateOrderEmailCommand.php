<?php
/**
 * @category  PHP
 * @package   Ruroc\TomasCodeTest
 * @author    Tomas Baranauskas <tomas.baranauskas@efendi.lt>
 * @copyright ©2020 Ruroc
 * @license   Ruroc
 * @link      https://www.ruroc.com/
 */

namespace Ruroc\TomasCodeTest\Console\Command;

use Exception;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;

class UpdateOrderEmailCommand extends Command
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var FilterGroupBuilderFactory
     */
    protected $filterGroupBuilderFactory;

    /**
     * @var FilterBuilderFactory
     */
    protected $filterBuilderFactory;

    /**
     * UpdateOrderEmailCommand constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterGroupBuilderFactory $filterGroupBuilderFactory
     * @param FilterBuilderFactory $filterBuilderFactory
     * @param string|null $name
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        FilterBuilderFactory $filterBuilderFactory,
        string $name = null
    ) {
        parent::__construct($name);

        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->filterBuilderFactory = $filterBuilderFactory;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('ruroc:tomas-code-test:update-order-email');
        $this->setDescription('Updates email address associated with the order');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $answer = $questionHelper->ask($input, $output, new Question("{$this->getEnterOrderIdMessage()}: "));

        // Todo: The next block of code should be abstracted out into a service;
        // not implemented in here due to simplification reasons.
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        /** @var FilterGroupBuilder $filterGroupBuilder */
        $filterGroupBuilder = $this->filterGroupBuilderFactory->create();

        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = $this->filterBuilderFactory->create();

        $filterGroupBuilder
            ->addFilter($filterBuilder->setField('entity_id')->setValue($answer)->create())
            ->addFilter($filterBuilder->setField('customer_email')->setValue($answer)->create());

        $searchCriteriaBuilder->setFilterGroups([$filterGroupBuilder->create()]);
        $orders = $this->orderRepository->getList($searchCriteriaBuilder->create());

        $totalCount = $orders->getTotalCount();
        if ($totalCount === 0) {
            $output->writeln($this->getNoResultsFoundMessage());

            return;
        }

        $output->writeln(sprintf($this->getResultFoundMessage(), $totalCount));
        $answer = $questionHelper->ask($input, $output, new Question("{$this->getUpdateEmailMessage()}: "));
        foreach ($orders as $order) {
            // Todo: A little validation won't hurt here.
            $order->setCustomerEmail($answer);
            try {
                $this->orderRepository->save($order);
            } catch (Exception $e) {
                // Todo: Log or output this, depending on the requirements.
            }
        }

        $output->writeln(sprintf($this->getUpdateSuccessMessage(), $totalCount));
    }

    /**
     * @return string
     */
    protected function getEnterOrderIdMessage()
    {
        return 'Please enter the order ID or an email address';
    }

    /**
     * @return string
     */
    protected function getUpdateEmailMessage()
    {
        return 'Please enter a new email address';
    }

    /**
     * @return string
     */
    protected function getUpdateSuccessMessage()
    {
        return '%s record(s) have been successfully updated';
    }

    /**
     * @return string
     */
    protected function getNoResultsFoundMessage()
    {
        return 'No orders found';
    }

    /**
     * @return string
     */
    protected function getResultFoundMessage()
    {
        return '%s result(s) found';
    }
}

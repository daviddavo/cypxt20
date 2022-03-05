<?php

namespace App\Controller\Admin;

use App\Entity\OnlineCall;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;

class OnlineCallCrudController extends AbstractCrudController
{
    public function __construct(private KernelInterface $appKernel, protected ManagerRegistry $managerRegistry, protected HttpClientInterface $client) {}

    public static function getEntityFqcn(): string
    {
        return OnlineCall::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('name', 'Nombre para');
        yield IntegerField::new('age');
        yield TextField::new('number');
        yield TextField::new('from_name', 'Nombre de');
        yield TextField::new('ip', 'Dirección IP');
        yield DateTimeField::new('created_at');
        yield TextareaField::new('comment');
    }

    private function oc2json(OnlineCall $oc)
    {
        $comment = mb_convert_encoding($oc->getComment(), 'UTF-8', 'UTF-8');
        // Remove newlines
        $comment = preg_replace('/\\\n/', ' ', $comment);
        $comment = trim(preg_replace('/\s+/', ' ', $comment));
        $comment = $comment ?: "% Sin comentarios";

        // Remove starting number
        $number = preg_replace('/^\+34/', '', $oc->getNumber());
        // No spaces
        $number = preg_replace('/\s*/', '', $number);

        return [
            'id'      => $oc->getId(),
            'name'    => $oc->getName(),
            'age'     => $oc->getAge(),
            'from'    => $oc->getFromName(),
            'number'  => $number,
            'comment' => $comment,
        ];
    }

    private function genCardProxyResponse($json): Response
    {
        // TODO: Cache this
        $url = $this->getParameter('app.templatex_url').'/pdf/cards';
        $response = $this->client->request('POST', $url, ['json' => $json]);

        $body = $response->getContent(false);
        $statusCode = $response->getStatusCode();
        $headers = $response->getHeaders(false);

        return new Response($body, $statusCode, $headers);
    }

    public function generateCard(AdminContext $context): Response
    {
        $instance = $context->getEntity()->getInstance();
        return $this->genCardProxyResponse(['table'=>[$this->oc2json($instance)]]);
    }

    public function generateCards(BatchActionDto $batchActionDto): Response
    {
        $repo = $this->managerRegistry->getRepository($batchActionDto->getEntityFqcn());
        $jsonTable = array_map(fn($id) => $this->oc2json($repo->find($id)), $batchActionDto->getEntityIds());

        return $this->genCardProxyResponse(['table'=>$jsonTable]);
    }

    public function generateAllCards(): Response
    {
        $repo = $this->managerRegistry->getRepository($this->getEntityFqcn());
        $jsonTable = array_map(fn($u) => $this->oc2json($u), $repo->findAll());
    
        return $this->genCardProxyResponse(['table'=>$jsonTable]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $genCard = Action::new('generateCard', 'Generar Tarjeta', 'fas fa-file-pdf')
            ->linkToCrudAction('generateCard')
            ->setHtmlAttributes(['target' => '_blank'])
            ->addCssClass('btn btn-success');
        $genCards = Action::new('generateCards', 'Generar Tarjetas', 'fas fa-file-pdf')
            ->linkToCrudAction('generateCards')
            ->setHtmlAttributes(['target' => '_blank'])
            ->addCssClass('btn btn-success disable-modal');
        $genAllCards = Action::new('generateAllCards', 'Generar todas las Tarjetas', 'fas fa-file-pdf')
            ->linkToCrudAction('generateAllCards')
            ->setHtmlAttributes(['target' => '_blank'])
            ->createAsGlobalAction()
            ->addCssClass('btn btn-success');
        
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $genCard)
            ->add(Crud::PAGE_INDEX, $genAllCards)
            ->add(Crud::PAGE_DETAIL, $genCard)
            ->addBatchAction($genCards);
    }
}
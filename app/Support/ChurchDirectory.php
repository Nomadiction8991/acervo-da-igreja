<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * @phpstan-type PublicField array{label: string, value: string}
 * @phpstan-type Document array{title: string, group: string, type: string, visibility: string, updated_at: string}
 * @phpstan-type Photo array{label: string, visibility: string}
 * @phpstan-type ChurchRecord array{
 *     slug: string,
 *     name: string,
 *     legal_name: string,
 *     summary: string,
 *     city: string,
 *     state: string,
 *     district: string,
 *     zip: string,
 *     address: string,
 *     control_code: string,
 *     registration: string,
 *     tone: string,
 *     synced_files: int,
 *     pending_tasks: int,
 *     sync_status: string,
 *     public_fields: list<PublicField>,
 *     private_fields_count: int,
 *     documents: list<Document>,
 *     gallery: list<Photo>
 * }
 * @phpstan-type Metrics array{
 *     public_documents: int,
 *     public_photos: int,
 *     private_documents: int,
 *     private_photos: int,
 *     pending_tasks: int,
 *     sync_status: string,
 *     synced_files: int
 * }
 * @phpstan-type Church array{
 *     slug: string,
 *     name: string,
 *     legal_name: string,
 *     summary: string,
 *     city: string,
 *     state: string,
 *     district: string,
 *     zip: string,
 *     address: string,
 *     control_code: string,
 *     registration: string,
 *     tone: string,
 *     synced_files: int,
 *     pending_tasks: int,
 *     sync_status: string,
 *     public_fields: list<PublicField>,
 *     private_fields_count: int,
 *     documents: list<Document>,
 *     gallery: list<Photo>,
 *     public_documents: list<Document>,
 *     public_gallery: list<Photo>,
 *     private_documents_count: int,
 *     private_gallery_count: int,
 *     metrics: Metrics
 * }
 * @phpstan-type CityGroup array{
 *     city: string,
 *     state: string,
 *     church_count: int,
 *     public_documents: int,
 *     public_photos: int,
 *     churches: array<int, Church>
 * }
 * @phpstan-type PortalStats array{
 *     churches: int,
 *     cities: int,
 *     public_documents: int,
 *     public_photos: int,
 *     synced_files: int,
 *     pending_tasks: int
 * }
 */
final class ChurchDirectory
{
    /**
     * @return Collection<int, Church>
     */
    public function all(): Collection
    {
        return collect($this->records())->map(function (array $church): array {
            $publicDocuments = array_values(array_filter(
                $church['documents'],
                static fn (array $document): bool => $document['visibility'] === 'Público',
            ));

            $publicGallery = array_values(array_filter(
                $church['gallery'],
                static fn (array $photo): bool => $photo['visibility'] === 'Público',
            ));

            $privateDocuments = count($church['documents']) - count($publicDocuments);
            $privatePhotos = count($church['gallery']) - count($publicGallery);

            return [
                ...$church,
                'public_documents' => $publicDocuments,
                'public_gallery' => $publicGallery,
                'private_documents_count' => $privateDocuments,
                'private_gallery_count' => $privatePhotos,
                'metrics' => [
                    'public_documents' => count($publicDocuments),
                    'public_photos' => count($publicGallery),
                    'private_documents' => $privateDocuments,
                    'private_photos' => $privatePhotos,
                    'pending_tasks' => $church['pending_tasks'],
                    'sync_status' => $church['sync_status'],
                    'synced_files' => $church['synced_files'],
                ],
            ];
        })->values();
    }

    /**
     * @return Collection<int, CityGroup>
     */
    public function cityGroups(): Collection
    {
        return $this->all()
            ->groupBy('city')
            ->map(function (Collection $churches, string $city): array {
                /** @var Church $firstChurch */
                $firstChurch = $churches->firstOrFail();

                return [
                    'city' => $city,
                    'state' => $firstChurch['state'],
                    'church_count' => $churches->count(),
                    'public_documents' => $churches->sum(
                        static fn (array $church): int => $church['metrics']['public_documents'],
                    ),
                    'public_photos' => $churches->sum(
                        static fn (array $church): int => $church['metrics']['public_photos'],
                    ),
                    'churches' => $churches->values()->all(),
                ];
            })
            ->values();
    }

    /**
     * @return Church|null
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }

    /**
     * @return PortalStats
     */
    public function portalStats(): array
    {
        $churches = $this->all();

        return [
            'churches' => $churches->count(),
            'cities' => $churches->pluck('city')->unique()->count(),
            'public_documents' => $churches->sum(
                static fn (array $church): int => $church['metrics']['public_documents'],
            ),
            'public_photos' => $churches->sum(
                static fn (array $church): int => $church['metrics']['public_photos'],
            ),
            'synced_files' => $churches->sum(
                static fn (array $church): int => $church['metrics']['synced_files'],
            ),
            'pending_tasks' => $churches->sum(
                static fn (array $church): int => $church['metrics']['pending_tasks'],
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $totals = $this->portalStats();
        $churches = $this->all();
        /** @var Church $featuredChurch */
        $featuredChurch = $churches->firstOrFail();

        return [
            'summary' => [
                [
                    'label' => 'Igrejas acompanhadas',
                    'value' => $totals['churches'],
                    'detail' => $totals['cities'].' cidades com portal ativo',
                ],
                [
                    'label' => 'Documentos públicos',
                    'value' => $totals['public_documents'],
                    'detail' => 'Filtrados pelo backend por visibilidade',
                ],
                [
                    'label' => 'Arquivos sincronizados',
                    'value' => $totals['synced_files'],
                    'detail' => 'Servidor + Google Drive em modelo híbrido',
                ],
                [
                    'label' => 'Pendências abertas',
                    'value' => $totals['pending_tasks'],
                    'detail' => 'Demandas privadas da secretaria interna',
                ],
            ],
            'tasks' => [
                [
                    'title' => 'Atualizar alvará da sede central',
                    'church' => 'Renovo Centro',
                    'priority' => 'Alta',
                    'due' => '05 abr 2026',
                    'assignee' => 'Ana Paula',
                    'status' => 'Em andamento',
                ],
                [
                    'title' => 'Conferir fotos do retiro infantil',
                    'church' => 'Vida Nova CPA',
                    'priority' => 'Média',
                    'due' => '07 abr 2026',
                    'assignee' => 'Lucas Ramos',
                    'status' => 'Aguardando',
                ],
                [
                    'title' => 'Separar documentos públicos de fundação',
                    'church' => 'Esperança Várzea Grande',
                    'priority' => 'Baixa',
                    'due' => '12 abr 2026',
                    'assignee' => 'Marina Lopes',
                    'status' => 'Triagem',
                ],
            ],
            'sync' => [
                [
                    'account' => 'Drive Institucional',
                    'folder' => '/Igrejas/Cuiaba',
                    'status' => 'Sincronizado',
                    'detail' => '42 arquivos espelhados e saudáveis',
                ],
                [
                    'account' => 'Drive Arquivo Regional',
                    'folder' => '/Regional/VG',
                    'status' => 'Pendente',
                    'detail' => '3 documentos aguardando reenvio',
                ],
                [
                    'account' => 'Drive Histórico',
                    'folder' => '/Arquivo/Historico',
                    'status' => 'Erro',
                    'detail' => 'Falha de autenticação detectada hoje',
                ],
            ],
            'logs' => [
                [
                    'when' => 'Hoje, 09:14',
                    'user' => 'Marina Lopes',
                    'action' => 'Alterou visibilidade',
                    'module' => 'Documentos',
                    'target' => 'Ata da assembleia 2025',
                ],
                [
                    'when' => 'Hoje, 08:47',
                    'user' => 'Ana Paula',
                    'action' => 'Criou tarefa',
                    'module' => 'Tarefas',
                    'target' => 'Atualizar alvará da sede central',
                ],
                [
                    'when' => 'Ontem, 18:02',
                    'user' => 'Pr. Daniel',
                    'action' => 'Redefiniu permissão',
                    'module' => 'Usuários',
                    'target' => 'Equipe de secretaria',
                ],
            ],
            'permissions' => [
                [
                    'group' => 'Igrejas',
                    'granted' => '4/4',
                    'detail' => 'Criar, editar, excluir e visualizar',
                ],
                [
                    'group' => 'Documentos',
                    'granted' => '5/5',
                    'detail' => 'Grupo, anexo, edição, exclusão e visibilidade',
                ],
                [
                    'group' => 'Tarefas',
                    'granted' => '4/4',
                    'detail' => 'Criar, editar, excluir e concluir',
                ],
                [
                    'group' => 'Logs',
                    'granted' => '1/1',
                    'detail' => 'Auditoria liberada para administradores',
                ],
            ],
            'featured_church' => $featuredChurch,
            'cities' => $this->cityGroups()->all(),
        ];
    }

    /**
     * @return list<ChurchRecord>
     */
    private function records(): array
    {
        return [
            [
                'slug' => 'renovo-centro',
                'name' => 'Igreja Renovo Centro',
                'legal_name' => 'Comunidade Evangelica Renovo de Cuiaba',
                'summary' => 'Comunidade central com acervo ativo, equipe administrativa completa e publicacao bem controlada por visibilidade.',
                'city' => 'Cuiaba',
                'state' => 'MT',
                'district' => 'Centro Sul',
                'zip' => '78005-200',
                'address' => 'Rua Treze de Junho, 610',
                'control_code' => 'IGR-041',
                'registration' => '24.876.120/0001-50',
                'tone' => 'cobalt',
                'synced_files' => 42,
                'pending_tasks' => 2,
                'sync_status' => 'Sincronizado',
                'public_fields' => [
                    ['label' => 'Nome fantasia', 'value' => 'Igreja Renovo Centro'],
                    ['label' => 'Cidade', 'value' => 'Cuiaba - MT'],
                    ['label' => 'Endereco', 'value' => 'Rua Treze de Junho, 610'],
                    ['label' => 'CEP', 'value' => '78005-200'],
                ],
                'private_fields_count' => 4,
                'documents' => [
                    ['title' => 'Estatuto consolidado', 'group' => 'Institucional', 'type' => 'PDF', 'visibility' => 'Público', 'updated_at' => '20 mar 2026'],
                    ['title' => 'Ata de fundacao', 'group' => 'Historico', 'type' => 'PDF', 'visibility' => 'Público', 'updated_at' => '18 mar 2026'],
                    ['title' => 'Comprovante bancario', 'group' => 'Financeiro', 'type' => 'PDF', 'visibility' => 'Privado', 'updated_at' => '29 mar 2026'],
                    ['title' => 'Certidao do imovel', 'group' => 'Patrimonio', 'type' => 'PDF', 'visibility' => 'Privado', 'updated_at' => '25 mar 2026'],
                ],
                'gallery' => [
                    ['label' => 'Fachada principal', 'visibility' => 'Público'],
                    ['label' => 'Auditorio principal', 'visibility' => 'Público'],
                    ['label' => 'Acao social', 'visibility' => 'Público'],
                    ['label' => 'Arquivo interno', 'visibility' => 'Privado'],
                ],
            ],
            [
                'slug' => 'vida-nova-cpa',
                'name' => 'Igreja Vida Nova CPA',
                'legal_name' => 'Ministerio Vida Nova Regiao Norte',
                'summary' => 'Unidade com alto volume de fotos publicas e demandas frequentes de documentos por grupos ministeriais.',
                'city' => 'Cuiaba',
                'state' => 'MT',
                'district' => 'CPA II',
                'zip' => '78055-718',
                'address' => 'Avenida Brasil, 1290',
                'control_code' => 'IGR-056',
                'registration' => '31.004.550/0001-09',
                'tone' => 'olive',
                'synced_files' => 28,
                'pending_tasks' => 3,
                'sync_status' => 'Pendente',
                'public_fields' => [
                    ['label' => 'Nome fantasia', 'value' => 'Igreja Vida Nova CPA'],
                    ['label' => 'Cidade', 'value' => 'Cuiaba - MT'],
                    ['label' => 'Endereco', 'value' => 'Avenida Brasil, 1290'],
                    ['label' => 'CEP', 'value' => '78055-718'],
                ],
                'private_fields_count' => 4,
                'documents' => [
                    ['title' => 'Carta de apresentacao', 'group' => 'Institucional', 'type' => 'PDF', 'visibility' => 'Público', 'updated_at' => '14 mar 2026'],
                    ['title' => 'Calendario de eventos', 'group' => 'Comunicacao', 'type' => 'PDF', 'visibility' => 'Público', 'updated_at' => '28 mar 2026'],
                    ['title' => 'Contrato de locacao', 'group' => 'Patrimonio', 'type' => 'PDF', 'visibility' => 'Privado', 'updated_at' => '30 mar 2026'],
                ],
                'gallery' => [
                    ['label' => 'Entrada lateral', 'visibility' => 'Público'],
                    ['label' => 'Galeria de voluntarios', 'visibility' => 'Público'],
                    ['label' => 'Encontro de jovens', 'visibility' => 'Público'],
                    ['label' => 'Classe infantil', 'visibility' => 'Público'],
                ],
            ],
            [
                'slug' => 'esperanca-vg',
                'name' => 'Igreja Esperanca Varzea Grande',
                'legal_name' => 'Comunidade Crista Esperanca de Varzea Grande',
                'summary' => 'Comunidade com documentacao historica forte e revisao recorrente de visibilidade para o portal publico.',
                'city' => 'Varzea Grande',
                'state' => 'MT',
                'district' => 'Centro Norte',
                'zip' => '78110-420',
                'address' => 'Rua Capitao Costa, 242',
                'control_code' => 'IGR-018',
                'registration' => '18.220.301/0001-77',
                'tone' => 'wine',
                'synced_files' => 17,
                'pending_tasks' => 1,
                'sync_status' => 'Erro',
                'public_fields' => [
                    ['label' => 'Nome fantasia', 'value' => 'Igreja Esperanca Varzea Grande'],
                    ['label' => 'Cidade', 'value' => 'Varzea Grande - MT'],
                    ['label' => 'Endereco', 'value' => 'Rua Capitao Costa, 242'],
                    ['label' => 'CEP', 'value' => '78110-420'],
                ],
                'private_fields_count' => 4,
                'documents' => [
                    ['title' => 'Historia da comunidade', 'group' => 'Historico', 'type' => 'PDF', 'visibility' => 'Público', 'updated_at' => '16 mar 2026'],
                    ['title' => 'Regimento interno', 'group' => 'Institucional', 'type' => 'PDF', 'visibility' => 'Público', 'updated_at' => '27 mar 2026'],
                    ['title' => 'Planilha de manutencao', 'group' => 'Administrativo', 'type' => 'XLS', 'visibility' => 'Privado', 'updated_at' => '31 mar 2026'],
                ],
                'gallery' => [
                    ['label' => 'Jardim frontal', 'visibility' => 'Público'],
                    ['label' => 'Galeria de eventos', 'visibility' => 'Público'],
                    ['label' => 'Sala de arquivo', 'visibility' => 'Privado'],
                ],
            ],
            [
                'slug' => 'luz-do-pantanal',
                'name' => 'Igreja Luz do Pantanal',
                'legal_name' => 'Ministerio Luz do Pantanal de Chapada',
                'summary' => 'Base regional com presenca mais enxuta, ideal para demonstrar a versao mobile do portal e operacao de campo.',
                'city' => 'Chapada dos Guimaraes',
                'state' => 'MT',
                'district' => 'Centro',
                'zip' => '78195-000',
                'address' => 'Praca Dom Wunibaldo, 18',
                'control_code' => 'IGR-073',
                'registration' => '42.119.004/0001-18',
                'tone' => 'brass',
                'synced_files' => 11,
                'pending_tasks' => 2,
                'sync_status' => 'Sincronizado',
                'public_fields' => [
                    ['label' => 'Nome fantasia', 'value' => 'Igreja Luz do Pantanal'],
                    ['label' => 'Cidade', 'value' => 'Chapada dos Guimaraes - MT'],
                    ['label' => 'Endereco', 'value' => 'Praca Dom Wunibaldo, 18'],
                    ['label' => 'CEP', 'value' => '78195-000'],
                ],
                'private_fields_count' => 4,
                'documents' => [
                    ['title' => 'Apresentacao institucional', 'group' => 'Institucional', 'type' => 'PDF', 'visibility' => 'Público', 'updated_at' => '23 mar 2026'],
                    ['title' => 'Mapa de patrimonio', 'group' => 'Patrimonio', 'type' => 'PDF', 'visibility' => 'Privado', 'updated_at' => '24 mar 2026'],
                ],
                'gallery' => [
                    ['label' => 'Vista da fachada', 'visibility' => 'Público'],
                    ['label' => 'Celebração regional', 'visibility' => 'Público'],
                    ['label' => 'Deposito interno', 'visibility' => 'Privado'],
                ],
            ],
        ];
    }
}

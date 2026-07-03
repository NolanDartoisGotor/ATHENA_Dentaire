<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\OpeningHour;
use App\Entity\PageSection;
use App\Entity\Service;
use App\Entity\SiteSettings;
use App\Entity\TeamMember;
use App\Entity\TechnicalFacility;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadSiteSettings($manager);
        $this->loadPageSections($manager);
        $this->loadOpeningHours($manager);
        $this->loadTechnicalFacilities($manager);
        $this->loadServices($manager);
        $this->loadTeamMembers($manager);
        $this->loadUsers($manager);

        $manager->flush();
    }

    private function loadSiteSettings(ObjectManager $manager): void
    {
        $settings = (new SiteSettings())
            ->setOrganizationName('Cabinet Athéna Dentaire')
            ->setTagline('Votre santé bucco-dentaire, notre priorité.')
            ->setAddress('1, rue Jean Addams 14280 Saint-Contest')
            ->setPhone('02 31 85 22 40')
            ->setEmergencyPhone('02 31 86 13 72')
            ->setContactEmail('contact@athena-dentaire.fr')
            ->setFormRecipientEmail('contact@athena-dentaire.fr')
            ->setHeroImage('images/hero.jpg')
            ->setMapImage('images/map.jpg')
            ->setAccessPlanImage('images/access-plan.jpg')
            ->setAssistantsList('Céline • Sabrina • Christine • Céline • Océane • Maryline • Chloé')
            ->setContactFormNotice('Aucune prise de rendez-vous ou annulation ne sera traitée via ce formulaire. Merci de contacter le secrétariat par téléphone.')
            ->setFooterText('© 2024 Cabinet Athéna Dentaire - Mentions Légales - Politique de Confidentialité - Site réalisé par GWM Agency');

        $manager->persist($settings);
    }

    private function loadPageSections(ObjectManager $manager): void
    {
        $sections = [
            PageSection::KEY_APPOINTMENT => [
                'title' => 'Prendre rendez-vous',
                'subtitle' => null,
                'body' => "La prise de rendez-vous s'effectue exclusivement par téléphone.",
            ],
            PageSection::KEY_EMERGENCY => [
                'title' => "Service d'Urgences",
                'subtitle' => null,
                'body' => "Un service d'urgence est mis à la disposition exclusive des patients déjà suivis par le cabinet.",
            ],
            PageSection::KEY_BEFORE_CONSULTATION => [
                'title' => 'Avant votre consultation',
                'subtitle' => null,
                'body' => "🪪 Carte Vitale à jour et carte de mutuelle à chaque rendez-vous.\n⚠ Rendez-vous non honorés : nous nous réservons le droit de refuser de nouveaux rendez-vous.\nLe cabinet est placé sous vidéosurveillance.",
            ],
            PageSection::KEY_TECHNICAL_INTRO => [
                'title' => 'Un plateau technique de pointe',
                'subtitle' => 'Notre cabinet est conçu pour vous offrir des soins dans un environnement moderne, sécurisé et apaisant.',
                'body' => '',
            ],
            PageSection::KEY_SERVICES_INTRO => [
                'title' => 'Nos pôles de compétences',
                'subtitle' => null,
                'body' => '',
            ],
            PageSection::KEY_TEAM_INTRO => [
                'title' => 'Notre Équipe',
                'subtitle' => "Nos praticiens exercent en chirurgie dentaire omnipratique et en implantologie orale, accompagnés d'une équipe d'assistantes qualifiées.",
                'body' => '',
            ],
            PageSection::KEY_LEGAL_NOTICE => [
                'title' => 'Mentions légales',
                'subtitle' => null,
                'body' => <<<'HTML'
<p><strong>Éditeur du site</strong></p>
<p>Cabinet Athéna Dentaire<br>1, rue Jean Addams<br>14280 Saint-Contest<br>Téléphone : 02 31 85 22 40<br>Email : contact@athena-dentaire.fr</p>

<h2>Directeur de la publication</h2>
<p>Dr Rémi BRUNSCHVICG, en sa qualité de représentant du cabinet.</p>

<h2>Hébergement</h2>
<p>Le site est hébergé par l'hébergeur désigné par le prestataire technique du cabinet. Pour toute question relative à l'hébergement, vous pouvez nous contacter à l'adresse indiquée ci-dessus.</p>

<h2>Propriété intellectuelle</h2>
<p>L'ensemble des éléments composant ce site (textes, images, graphismes, logo, icônes) est la propriété exclusive du Cabinet Athéna Dentaire ou de ses partenaires. Toute reproduction, représentation, modification ou exploitation, totale ou partielle, sans autorisation écrite préalable est interdite.</p>

<h2>Responsabilité</h2>
<p>Les informations diffusées sur ce site sont fournies à titre indicatif. Le cabinet s'efforce d'en assurer l'exactitude mais ne saurait être tenu responsable d'erreurs, d'omissions ou d'une indisponibilité temporaire du site.</p>

<h2>Liens hypertextes</h2>
<p>La création de liens vers ce site est soumise à l'accord préalable du cabinet. Les liens vers des sites tiers n'engagent pas la responsabilité du Cabinet Athéna Dentaire quant à leur contenu.</p>
HTML,
            ],
            PageSection::KEY_PRIVACY_POLICY => [
                'title' => 'Politique de confidentialité',
                'subtitle' => null,
                'body' => <<<'HTML'
<p>Le Cabinet Athéna Dentaire accorde une grande importance à la protection de vos données personnelles. La présente politique décrit comment nous collectons, utilisons et protégeons vos informations conformément au Règlement Général sur la Protection des Données (RGPD).</p>

<h2>Responsable du traitement</h2>
<p>Cabinet Athéna Dentaire<br>1, rue Jean Addams<br>14280 Saint-Contest<br>Email : contact@athena-dentaire.fr</p>

<h2>Données collectées</h2>
<p>Nous collectons uniquement les données que vous nous transmettez volontairement via le formulaire de contact du site :</p>
<ul>
    <li>Nom et prénom</li>
    <li>Adresse email</li>
    <li>Sujet et contenu du message</li>
</ul>
<p>Ce site vitrine ne collecte pas de données de navigation à des fins publicitaires et n'utilise pas de cookies de traçage.</p>

<h2>Finalité du traitement</h2>
<p>Vos données sont utilisées exclusivement pour répondre à votre demande de contact. Elles ne sont ni vendues, ni cédées à des tiers à des fins commerciales.</p>

<h2>Base légale</h2>
<p>Le traitement repose sur votre consentement, exprimé lors de l'envoi du formulaire de contact.</p>

<h2>Durée de conservation</h2>
<p>Les messages reçus via le formulaire sont conservés pendant la durée nécessaire au traitement de votre demande, puis archivés ou supprimés conformément aux obligations légales applicables.</p>

<h2>Vos droits</h2>
<p>Conformément au RGPD, vous disposez des droits suivants concernant vos données personnelles :</p>
<ul>
    <li>Droit d'accès</li>
    <li>Droit de rectification</li>
    <li>Droit à l'effacement</li>
    <li>Droit à la limitation du traitement</li>
    <li>Droit d'opposition</li>
    <li>Droit à la portabilité</li>
</ul>
<p>Pour exercer ces droits, contactez-nous à l'adresse email indiquée ci-dessus. Vous pouvez également introduire une réclamation auprès de la CNIL (www.cnil.fr).</p>

<h2>Sécurité</h2>
<p>Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données contre tout accès non autorisé, perte ou divulgation.</p>
HTML,
            ],
        ];

        foreach ($sections as $key => $data) {
            $section = (new PageSection())
                ->setSectionKey($key)
                ->setTitle($data['title'])
                ->setSubtitle($data['subtitle'])
                ->setBody($data['body']);
            $manager->persist($section);
        }
    }

    private function loadOpeningHours(ObjectManager $manager): void
    {
        $hours = [
            ['Lundi & Mercredi', '9h00 - 12h00 | 14h00 - 18h00', 1],
            ['Mardi', '9h00 - 12h00 | 15h00 - 18h00', 2],
            ['Jeudi', '9h00 - 12h00 | 14h00 - 17h00', 3],
            ['Vendredi', '9h00 - 12h00', 4],
        ];

        foreach ($hours as [$day, $time, $position]) {
            $openingHour = (new OpeningHour())
                ->setDayLabel($day)
                ->setHours($time)
                ->setPosition($position);
            $manager->persist($openingHour);
        }
    }

    private function loadTechnicalFacilities(ObjectManager $manager): void
    {
        $facilities = [
            ['6 Salles de soins', 'Des espaces ergonomiques équipés des dernières technologies.', 'images/facility-1.jpg', 'large', 1],
            ['Bloc opératoire', 'Salle de chirurgie dédiée aux interventions complexes (implantologie).', 'images/facility-2.jpg', 'large', 2],
            ['Salle de stérilisation', 'Hygiène absolue et traçabilité rigoureuse.', 'images/facility-3.jpg', 'standard', 3],
            ['Imagerie Médicale', 'Panoramique et Cone Beam 3D pour diagnostics précis.', 'images/facility-4.jpg', 'standard', 4],
            ['Salle de repos', 'Confort post-opératoire après une intervention chirurgicale.', 'images/facility-5.jpg', 'standard', 5],
        ];

        foreach ($facilities as [$title, $description, $image, $layout, $position]) {
            $facility = (new TechnicalFacility())
                ->setTitle($title)
                ->setDescription($description)
                ->setImage($image)
                ->setLayout($layout)
                ->setPosition($position);
            $manager->persist($facility);
        }
    }

    private function loadServices(ObjectManager $manager): void
    {
        $services = [
            ['Implants dentaires', 'Remplacement de dents manquantes par des racines en titane, pour un confort durable.', 'images/icon-bone.svg', 1],
            ['Prothèses', 'Couronnes, bridges et prothèses amovibles sur-mesure pour restaurer votre sourire.', 'images/icon-smile.svg', 2],
            ['Soins conventionnels', 'Dentisterie conservatrice, caries, détartrage et prévention au quotidien.', 'images/icon-stethoscope.svg', 3],
            ['Esthétique du sourire', 'Éclaircissement, facettes et réhabilitations esthétiques pour un sourire harmonieux.', 'images/icon-sparkles.svg', 4],
        ];

        foreach ($services as [$title, $description, $icon, $position]) {
            $service = (new Service())
                ->setTitle($title)
                ->setDescription($description)
                ->setIcon($icon)
                ->setPosition($position);
            $manager->persist($service);
        }
    }

    private function loadTeamMembers(ObjectManager $manager): void
    {
        $members = [
            ['Dr Rémi BRUNSCHVICG', 'Chirurgien-Dentiste (Titulaire)', 'images/team-1.jpg', 1],
            ['Dr Alexis HUET LAIGNEAU', 'Chirurgien-Dentiste (Titulaire)', 'images/team-2.jpg', 2],
            ['Dr Julien POULINGUE', 'Chirurgien-Dentiste (Titulaire)', 'images/team-3.jpg', 3],
            ['Dr Eric LE BESNERAIS', 'Chirurgien-Dentiste (Titulaire)', 'images/team-4.jpg', 4],
            ['Dr Willyam LEPLONGEON', 'Collaborateur libéral', 'images/team-5.jpg', 5],
        ];

        foreach ($members as [$name, $role, $photo, $position]) {
            $member = (new TeamMember())
                ->setFullName($name)
                ->setRole($role)
                ->setPhoto($photo)
                ->setPosition($position);
            $manager->persist($member);
        }
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setEmail('admin@athena-dentaire.fr')
            ->setFullName('Administrateur Athéna')
            ->setRoles([User::ROLE_ADMIN]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'AdminAthena2026!'));
        $manager->persist($admin);

        $editor = (new User())
            ->setEmail('editor@athena-dentaire.fr')
            ->setFullName('Éditeur Athéna')
            ->setRoles([User::ROLE_EDITOR]);
        $editor->setPassword($this->passwordHasher->hashPassword($editor, 'EditorAthena2026!'));
        $manager->persist($editor);
    }
}

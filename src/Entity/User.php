<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *  normalizationContext={"groups"={"users_read"}},
 * 
 *  collectionOperations={"GET", "POST", "ResetPass"={
 *      "method"="post",
 *      "path"="/reset/{token}/ResetPass",
 *      "controller"="App\Controller\user\ResetPasswordController",
 * },
 *       "newPass"={
 *              "method"="post",
 *              "path"="/users/newpass/{token}",
 *              "controller"="App\Controller\user\NewPasswordController",
 *               },
 *
 * },
 *  itemOperations={"GET", "PUT",
 *      "changeUserInformations"={
 *              "method"="post",
 *              "path"="users/update",
 *              "controller"="App\Controller\user\changeUserInformationsController",
 *              "read"=false,
 *               },
 *     "ForgotPass"={
 *      "method"="post",
 *      "path"="ForgotPass/{email}",
 *      "controller"="App\Controller\user\ForgotPasswordController",
 *     "read"=false,
 *      "swagger_context"={
 *          "summary"="Mail reset password",
 *          "description"="Envoie de mail pour reset password"
 *          }
 *      },
 *     "UserByEmail"={
        "method"="get",
 *      "path"="getUser/{email}",
 *      "controller"="App\Controller\user\UserByEmailController",
 *      "defaults"={"email"="email"},
 *      "read"=false,
 *     }
 *  }
 * )
 * @UniqueEntity("email",message="Cette adresse email existe déjà")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"customers_read","invoices_read", "invoices_subresource", "users_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"customers_read","invoices_read", "invoices_subresource", "users_read"})
     * @Assert\NotBlank(message="L'email doit etre renseigné !")
     * @Assert\Email(message="Le format de l'adresse email n'est pas valide !")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le mot de passe est obligatoire !")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read", "invoices_subresource", "users_read"})
     * @Assert\NotBlank(message="Le prénom doit etre renseigné !")
     * @Assert\Length(min=3, minMessage="Le prenom doit faire plus de 3 caractères !", max=255 , maxMessage="Le prénom doit faire moins de 255 caractères")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read", "invoices_subresource", "users_read"})
     * @Assert\NotBlank(message="Le nom doit etre renseigné !")
     * @Assert\Length(min=3, minMessage="Le nom doit faire plus de 3 caractères !", max=255 , maxMessage="Le nom doit faire moins de 255 caractères")
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer", mappedBy="user", cascade={"persist", "remove"})
     */
    private $customers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetPassToken;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setUser($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            // set the owning side to null (unless already changed)
            if ($customer->getUser() === $this) {
                $customer->setUser(null);
            }
        }

        return $this;
    }

    public function getResetPassToken(): ?string
    {
        return $this->resetPassToken;
    }

    public function setResetPassToken(?string $resetPassToken): self
    {
        $this->resetPassToken = $resetPassToken;

        return $this;
    }
}

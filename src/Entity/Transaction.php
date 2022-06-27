<?php
/**
 * Transaction entity.
 */

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transaction.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\Table(name="transaction")
 */
class Transaction
{
    /**
     * Primary key.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * Name.
     *
     * @ORM\Column(
     *     type="string",
     *     length=64,
     * )
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="3",
     *     max="64",
     * )
     */
    private string $name;

    /**
     * Date.
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type(type="\DateTimeInterface")
     *
     * @ORM\Column(type="date")
     */
    private ?DateTimeInterface $date;

    /**
     * Amount.
     *
     * @ORM\Column(type="integer")
     */
    private ?int $amount = 0;

    /**
     * Category.
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Category",
     *     fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Category $category;

    /**
     * Wallet.
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Wallet",
     *     fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Wallet $wallet;

    /**
     * Category.
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Payment",
     *     fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Payment $payment;

    /**
     * Operation.
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Operation",
     *     fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Operation $operation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $comment = null;

    /**
     * Created at.
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type(type="\DateTimeInterface")
     */
    private DateTimeInterface $createdAt;

    /**
     * Updated at.
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type(type="\DateTimeInterface")
     */
    private DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class)
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable= false)
     *
     * @Assert\NotBlank
     */
    private ?User $author;

    /**
     * Transaction constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->setUpdatedAt(new DateTime('now'));
        $this->setCreatedAt(new DateTime('now'));
    }

    /**
     * Getter for Id.
     *
     * @return int|null Result
     */
    public function getId(): ?int
    {
        if (!isset($this->id)) {
            return 0;
        }

        return $this->id;
    }
    // region name

    /**
     * Getter for Name.
     *
     * @return string|null Name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for Name.
     *
     * @param string $name Name
     *
     * @return Transaction
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // endregion
    // region data
    /**
     * Getter for Date.
     *
     * @return DateTimeInterface Date
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Setter for Date.
     *
     * @param DateTimeInterface $date Date
     *
     * @return Transaction
     */
    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    // endregion
    // region amount
    /**
     * Getter for Amount.
     *
     * @return int $amount Amount
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Setter for Amount.
     *
     * @param int $amount Amount
     *
     * @return Transaction
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    // endregion
    // region create update
    /**
     * Getter for Created At.
     *
     * @return DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Created at.
     *
     * @param DateTimeInterface $createdAt Created at
     *
     * @return Transaction
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Getter for Updated at.
     *
     * @return DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for Updated at.
     *
     * @param DateTimeInterface $updatedAt Updated at
     *
     * @return Transaction
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    // endregion

    /**
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    // endregion
    // region ManyToOne
    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     *
     * @return Transaction
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Getter for wallet.
     *
     * @return Wallet|null Wallet
     */
    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    /**
     * Setter for wallet.
     *
     * @param Wallet|null $wallet Wallet
     *
     * @return Transaction
     */
    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Getter for payment.
     *
     * @return Payment|null Wallet
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * Setter for payment.
     *
     * @param Payment|null $payment Payment
     *
     * @return Transaction
     */
    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Getter for operation.
     *
     * @return Operation|null Operation
     */
    public function getOperation(): ?Operation
    {
        return $this->operation;
    }

    /**
     * Setter for operation.
     *
     * @param Operation|null $operation Operation
     *
     * @return Transaction
     */
    public function setOperation(?Operation $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    // endregion

    /**
     * Getter for Comment.
     *
     * @return string|null options string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Setter for Comment.
     *
     * @param string|null options $comment
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag options $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param Tag options $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return User|null options user
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null options $author
     *
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}

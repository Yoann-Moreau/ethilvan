<?php

namespace App\Service;

use App\Repository\AdminNotificationRepository;

class NotificationService {

	private bool $new_admin_notifications = false;

	private AdminNotificationRepository $admin_notification_repository;


	public function __construct(AdminNotificationRepository $admin_notification_repository) {
		$this->admin_notification_repository = $admin_notification_repository;
		$this->checkNewAdminNotifications();
	}


	/**
	 * @return bool
	 */
	public function isNewAdminNotifications(): bool {
		return $this->new_admin_notifications;
	}

	/**
	 * @param bool $new_admin_notifications
	 */
	public function setNewAdminNotifications(bool $new_admin_notifications): void {
		$this->new_admin_notifications = $new_admin_notifications;
	}


	public function checkNewAdminNotifications(): void {
		$notification = $this->admin_notification_repository->findOneBy(['seen' => false]);

		$this->setNewAdminNotifications(!empty($notification));
	}

}

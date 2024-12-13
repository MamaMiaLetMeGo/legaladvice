import './bootstrap';

import Alpine from 'alpinejs';
import chat from './chat';
import lawyerChat from './lawyer-chat';
import lawyerActiveChats from './lawyer-active-chats';
import notifications from './notifications';

window.Alpine = Alpine;
Alpine.data('chat', chat);
Alpine.data('lawyerChat', lawyerChat);
Alpine.data('lawyerActiveChats', lawyerActiveChats);
Alpine.data('notifications', notifications);
Alpine.start();

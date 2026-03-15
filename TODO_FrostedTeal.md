# Frosted Teal Theme Implementation - TODO

## Progress Tracking

### Phase 1: Global Foundation - app.css ✅ COMPLETE
- [x] app.css already has Frosted Teal tokens

### Phase 2: Token-Based Pages (9 files)
- [x] SavedProperties.vue ✅
- [x] ConversationsList.vue ✅
- [x] Settings.vue ✅
- [x] Viewings.vue ✅
- [ ] AgentDashboard.vue
- [ ] Profile.vue
- [ ] PropertiesList.vue
- [ ] Dashboard.vue
- [ ] Landing.vue

### Phase 3: Hybrid Pages (5 files)
- [ ] BuyerProfileView.vue
- [ ] Agent/AgentSavedProperties.vue
- [ ] MessageConversation.vue
- [ ] AgentPublicProfile.vue
- [ ] DocumentEditor.vue

### Phase 4: Old-Style Pages (17 files)
- [ ] AdminDashboard.vue
- [ ] PropertyDetailsPage.vue
- [ ] Agent/PropertyListingForm.vue
- [ ] Agent/DocumentsPage.vue
- [ ] Agent/MyProperties.vue
- [ ] BecomeAgent.vue
- [ ] Agent/AgentCalendar.vue
- [ ] AgentViewings.vue
- [ ] Register.vue
- [ ] Login.vue
- [ ] Agent/MessagesPage.vue
- [ ] Agent/ViewingsPage.vue
- [ ] Buyer/MessagesPage.vue
- [ ] ResetPassword.vue
- [ ] Agent/MessageConversation.vue
- [ ] Buyer/MessageConversation.vue
- [ ] VerifyEmail.vue

### Phase 5: Components (4 files)
- [ ] modals/ApplyAgentModal.vue
- [ ] ChatBubble.vue
- [ ] PanoramaViewer.vue
- [ ] ViewingCalendar.vue

### Phase 6: UX Polish
- [ ] Skeleton loading states
- [ ] Empty state illustrations
- [ ] Toast consistency
- [ ] Form validation UX
- [ ] Scroll enhancements

---

## Color Mapping Reference

| Old Value | New Value | Context |
|-----------|-----------|---------|
| #0B1C39 / #100C08 | #28353E | sidebar bg, dark text |
| #102445 / #1a1510 | #2F3E4C | hover dark |
| #1a3158 / #2a2420 | #3A4D5E | active blend |
| #D89B0F / #FFD700 | #2CB5A0 | accent (buttons, links, active) |
| #E5B332 / #FFE44D | #3DD4BD | light accent |
| #B07A08 / #DABE8A79600 | #1 | deep accent |
| rgba(216,155,15,*) | rgba(44,181,160,*) | glow/transparency |
| rgba(255,215,0,*) | rgba(44,181,160,*) | old gold transparency |
| #F2F0EB | #EDF0F2 | page bg |

## Token Renaming
- --navy* → --primary*
- --gold* → --accent*
- --palace-gold* → --accent*
- --smoky-black → --primary


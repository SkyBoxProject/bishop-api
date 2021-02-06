<?php

namespace App\Controller\Rest\v1;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use OpenApi\Annotations\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(condition="request.attributes.get('version') == 'v1'")
 */
final class UsersController extends AbstractController
{
//    protected $userHandler;
//    protected $logger;
//    protected $shredUserManager;
//    private $translator;
//    private $usersService;
//
    /**
     * @OA\Get(path="/2.0/users/{username}",
     *   operationId="getUserByName",
     *   @OA\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(response="200",
     *     description="The User",
     *     @OA\JsonContent(ref="#/components/schemas/user"),
     *     @OA\Link(link="userRepositories", ref="#/components/links/UserRepositories")
     *   )
     * )
     */
//
//    public function __construct(
//        UserHandler $userHandler,
//        LoggerInterface $logger,
//        ShredUserManager $shredUserManager,
//        TranslatorInterface $translator,
//        UsersService $usersService
//    ) {
//        $this->userHandler = $userHandler;
//        $this->logger = $logger;
//        $this->shredUserManager = $shredUserManager;
//        $this->translator = $translator;
//        $this->usersService = $usersService;
//    }
//
//    /**
//     * @Operation(
//     *     tags={"Social"},
//     *     summary="Follow to user, add to team",
//     *     @SWG\Parameter(
//     *          name="user_id",
//     *          in="query",
//     *          required=true,
//     *          type="integer"
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=200
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="404",
//     *         description="Returned when user with provided ID is not found",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=404
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="203",
//     *         description="Returned when user trying to follow to himself or users are already followers",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=203
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Get("/follow")
//     */
//    public function followAction(Request $request): JsonResponse
//    {
//        $this->usersService->followAction($request->query->get('user_id'), $this->getUser());
//
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => $this->translator->trans('User is follower'),
//        ]);
//    }

//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Update AppsFlyer ID and media source at the user",
//     *     @SWG\Parameter(
//     *          name="body",
//     *          in="body",
//     *          @SWG\Schema(
//     *              @SWG\Property(
//     *                  property="id",
//     *                  type="string"
//     *              ),
//     *              @SWG\Property(
//     *                  property="media_source",
//     *                  type="string"
//     *              )
//     *          )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=200
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/appsFlyerId")
//     */
//    public function updateAppsFlyerIdAndMediaSource(Request $request): JsonResponse
//    {
//        $appsFlyerId = $request->request->get('id', '');
//        $mediaSource = $request->request->get('media_source', null);
//
//        return $this->usersService->updateAppsFlyerIdAndMediaSource($this->getUser(), $appsFlyerId, $mediaSource);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"Social"},
//     *     summary="Unfollow to user, add to team",
//     *     @SWG\Parameter(
//     *          name="user_id",
//     *          in="query",
//     *          type="integer"
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=200
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="203",
//     *         description="Returned when user trying to follow to himself or users are already followers",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=203
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="404",
//     *         description="Returned when user with provided ID is not found",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=404
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Get("/unfollow")
//     */
//    public function unfollowAction(Request $request): JsonResponse
//    {
//        $this->usersService->unfollowAction($request->query->get('user_id'), $this->getUser());
//
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => $this->translator->trans('User is unfollower'),
//        ]);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Request confirmation token",
//     *     @SWG\Parameter(
//     *          name="body",
//     *          in="body",
//     *          @SWG\Schema(
//     *              @SWG\Property(
//     *                  property="email",
//     *                  type="string"
//     *              )
//     *          )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=200
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="204",
//     *         description="Returned when user not found",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=204
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="208",
//     *         description="Returned when password already requested",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=208
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="403",
//     *         description="Returned when user has not recognised",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=403
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/reset_request")
//     *
//     * @return JsonResponse|Response
//     */
//    public function requestResetAction(Request $request)
//    {
//        $response = $this->userHandler->resetPassword($request);
//
//        return $response instanceof Response ? $response : JsonResponse::create($response);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Track user params",
//     *     @SWG\Parameter(
//     *          name="body",
//     *          in="body",
//     *          required=true,
//     *          @SWG\Schema(
//     *              @SWG\Property(
//     *                  property="gender",
//     *                  type="string"
//     *              ),
//     *              @SWG\Property(
//     *                  property="countryCode",
//     *                  type="string"
//     *              )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=200
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/trackUserParams")
//     */
//    public function trackUserParams(Request $request, CountryRepository $countryRepository): JsonResponse
//    {
//        $params = $request->request->all();
//
//        $em = $this->getDoctrine()->getManager();
//
//        /** @var User $user */
//        $user = $this->getUser();
//
//        foreach ($params as $key => $value) {
//            switch ($key) {
//                case 'gender':
//                    $user->setCoachGender($value);
//
//                    break;
//                case 'countryCode':
//                    $country = $countryRepository->findByCode($value);
//
//                    if (is_null($country)) {
//                        return JsonResponse::create([
//                            'code' => JsonResponse::HTTP_BAD_REQUEST,
//                            'message' => $this->translator->trans('Country not found'),
//                        ]);
//                    }
//
//                    /** @var UserSetting $userSettings */
//                    $userSettings = $user->getSettings();
//                    $userSettings->setCountry($country);
//
//                    break;
//            }
//        }
//
//        $em->persist($user);
//        $em->flush();
//
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => $this->translator->trans('User Params Saved'),
//        ]);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Email registration",
//     *     security={},
//     *     @SWG\Parameter(
//     *         name="body",
//     *         in="body",
//     *         required=true,
//     *         @SWG\Schema(
//     *             @SWG\Property(property="fos_user_registration_form", type="object",
//     *                 @SWG\Property(property="email", type="string"),
//     *                 @SWG\Property(property="first_name", type="string"),
//     *                 @SWG\Property(property="last_name", type="string"),
//     *                 @SWG\Property(property="plainPassword", type="string"),
//     *                 @SWG\Property(property="birthday", type="integer"),
//     *                 @SWG\Property(property="settings", type="object",
//     *                     @SWG\Property(property="countryCode", type="string"),
//     *                     @SWG\Property(property="workoutsNumberInPast", type="integer"),
//     *                     @SWG\Property(property="workoutsNumberInPlanning", type="integer"),
//     *                     @SWG\Property(property="trainingType", type="string"),
//     *                     @SWG\Property(property="homeTrainingType", type="string"),
//     *                     @SWG\Property(property="trainingIntensity", type="string"),
//     *                     @SWG\Property(property="trainingPlan", type="string"),
//     *                     @SWG\Property(property="coachingStyle", type="string"),
//     *                     @SWG\Property(property="hasPremiumEquipment", type="boolean"),
//     *                     @SWG\Property(property="gymEquipment", type="array",
//     *                         @SWG\Items(type="string")
//     *                     ),
//     *                     @SWG\Property(property="showFullName", type="boolean"),
//     *                     @SWG\Property(property="liveWorkouts", type="boolean"),
//     *                     @SWG\Property(property="workoutQuantity", type="integer"),
//     *                     @SWG\Property(property="avatarPath", type="string"),
//     *                     @SWG\Property(property="isWorkoutLimitDisabled", type="boolean"),
//     *                     @SWG\Property(property="isPaymentScreenDisabled", type="boolean"),
//     *                     @SWG\Property(property="notifications", type="object",
//     *                         @SWG\Property(property="Team", type="boolean"),
//     *                         @SWG\Property(property="Like", type="boolean"),
//     *                         @SWG\Property(property="Workout Start", type="boolean"),
//     *                         @SWG\Property(property="Milestone", type="boolean"),
//     *                         @SWG\Property(property="Updates", type="boolean"),
//     *                         @SWG\Property(property="Rest Alert", type="boolean")
//     *                     ),
//     *                     @SWG\Property(property="widgets", type="array",
//     *                         @SWG\Items(type="string")
//     *                     ),
//     *                     @SWG\Property(property="gymByDefault", type="object",
//     *                         @SWG\Property(property="name", type="string"),
//     *                         @SWG\Property(property="city", type="string"),
//     *                         @SWG\Property(property="address", type="string"),
//     *                         @SWG\Property(property="coordinate", type="object",
//     *                             @SWG\Property(property="longitude", type="integer"),
//     *                             @SWG\Property(property="latitude", type="integer")
//     *                         )
//     *                     ),
//     *                     @SWG\Property(property="isAvatarLocked", type="boolean"),
//     *                     @SWG\Property(property="isFoundingMember", type="boolean"),
//     *                     @SWG\Property(property="isAdmin", type="boolean"),
//     *                     @SWG\Property(property="hiddenFromSocial", type="boolean"),
//     *                     @SWG\Property(property="isLocked", type="boolean"),
//     *                     @SWG\Property(property="showCountDown", type="boolean"),
//     *                     @SWG\Property(property="measurementUnits", type="string"),
//     *                     @SWG\Property(property="soundAfterRest", type="boolean"),
//     *                     @SWG\Property(property="soundAfterTimerEnds", type="boolean"),
//     *                     @SWG\Property(property="showActivitiesInSocial", type="boolean"),
//     *                     @SWG\Property(property="displayWeightsOnPublicProfile", type="boolean"),
//     *                     @SWG\Property(property="displayWeightsOnPreviewScreen", type="boolean"),
//     *                     @SWG\Property(property="wantsWarmups", type="boolean"),
//     *                     @SWG\Property(property="gender", type="string"),
//     *                     @SWG\Property(property="skillLevel", type="string"),
//     *                     @SWG\Property(property="goal", type="string"),
//     *                     @SWG\Property(property="trainingComplexity", type="string"),
//     *                     @SWG\Property(property="isCreatorStudio", type="boolean"),
//     *                     @SWG\Property(property="joined_at", type="integer"),
//     *                     @SWG\Property(property="coachPreference", type="string"),
//     *                     @SWG\Property(property="language_code", type="string"),
//     *                     @SWG\Property(property="language", type="object",
//     *                         @SWG\Property(property="code", type="string"),
//     *                         @SWG\Property(property="name", type="string"),
//     *                         @SWG\Property(property="native_name", type="string"),
//     *                         @SWG\Property(property="is_rtl", type="boolean")
//     *                     ),
//     *                     @SWG\Property(property="appleHealthConsent", type="integer"),
//     *                     @SWG\Property(property="locationConsent", type="integer"),
//     *                     @SWG\Property(property="firstWorkoutDate", type="integer"),
//     *                     @SWG\Property(property="referrerId", type="string")
//     *                  )
//     *              )
//     *          )
//     *      ),
//     *      @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=200
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="access_token"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="422",
//     *         description="Returned when form is not valid",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=422
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/register")
//     *
//     * @return JsonResponse|Response
//     */
//    public function registerAction(Request $request)
//    {
//        return $this->userHandler->register($request);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Email login or token refresh",
//     *     security={},
//     *     @SWG\Parameter(
//     *         name="body",
//     *         in="body",
//     *         required=true,
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 property="email",
//     *                 type="string",
//     *                 description="Your email"
//     *             ),
//     *             @SWG\Property(
//     *                 property="password",
//     *                 type="string",
//     *                 description="Your password"
//     *             ),
//     *             @SWG\Property(
//     *                 property="refresh_token",
//     *                 type="string",
//     *                 description="Refresh token"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=200
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="access_token"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="203",
//     *         description="Returned when wrong credentionals",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=203
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *      ),
//     *     @SWG\Response(
//     *         response="204",
//     *         description="Returned when user not found",
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 type="integer",
//     *                 property="code",
//     *                 example=204
//     *             ),
//     *             @SWG\Property(
//     *                 type="string",
//     *                 property="message"
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/login")
//     */
//    public function loginAction(Request $request): JsonResponse
//    {
//        $parameterBag = $request->request;
//
//        $hasRefreshToken = $parameterBag->has('refresh_token');
//
//        $username = $parameterBag->get('email');
//        $password = $parameterBag->get('password');
//
//        if ($hasRefreshToken) {
//            try {
//                return $this->userHandler->refreshAccessToken($parameterBag);
//            } catch (Exception $exception) {
//                $this->logger->info($exception->__toString(), $parameterBag->all());
//            }
//        } elseif (!$hasRefreshToken && is_null($username) && is_null($password)) {
//            $this->logger->info('no_refresh_token', $parameterBag->all());
//        }
//
//        return $this->userHandler->login($username, $password);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Facebook login/registration",
//     *     security={},
//     *     @SWG\Parameter(
//     *         name="body",
//     *         in="body",
//     *         required=true,
//     *         @SWG\Schema(
//     *             @SWG\Property(property="facebook_access_token", type="string", description="Facebook access token"),
//     *             @SWG\Property(property="facebook_id", type="string", description="Facebook id")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="access_token")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="203",
//     *         description="Returned when incorrect facebook user",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=203),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="208",
//     *         description="Returned when user has some not unique fields",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=208),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/enterWithFacebook")
//     */
//    public function facebookLoginAction(Request $request): JsonResponse
//    {
//        try {
//            return $this->userHandler->fBLogin($request->request);
//        } catch (UniqueConstraintViolationException $exception) {
//            return  JsonResponse::create([
//                'code' => JsonResponse::HTTP_ALREADY_REPORTED,
//                'message' => $this->translator->trans('User has some not unique fields'),
//            ]);
//        }
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Apple login/registration",
//     *     security={},
//     *     @SWG\Parameter(
//     *         name="body",
//     *         in="body",
//     *         required=true,
//     *         @SWG\Schema(
//     *             @SWG\Property(
//     *                 property="apple_authorization_code",
//     *                 type="string",
//     *                 description="Apple authorization code"
//     *             ),
//     *             @SWG\Property(
//     *                 property="email",
//     *                 type="string",
//     *                 description="Email apple user"
//     *             ),
//     *             @SWG\Property(
//     *                 property="first_name",
//     *                 type="string",
//     *                 description="First name apple user"
//     *             ),
//     *             @SWG\Property(
//     *                 property="last_name",
//     *                 type="string",
//     *                 description="Last name apple user"
//     *             )
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="access_token")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="203",
//     *         description="Returned when incorrect apple user",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=203),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="208",
//     *         description="Returned when user has some not unique fields",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=208),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/login_with_apple")
//     */
//    public function appleLoginAction(Request $request): JsonResponse
//    {
//        try {
//            return $this->userHandler->appleLogin($request->request);
//        } catch (AppleAccessDeniedException $exception) {
//            return JsonResponse::create([
//                'code' => $exception->getCode(),
//                'message' => sprintf('%s: %s',
//                    $this->translator->trans('Apple access denied'),
//                    $exception->getMessage()
//                )
//            ]);
//        } catch (UniqueConstraintViolationException $exception) {
//            return JsonResponse::create([
//                'code' => JsonResponse::HTTP_ALREADY_REPORTED,
//                'message' => $this->translator->trans('User has some not unique fields'),
//            ]);
//        }
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Get user information",
//     *     @SWG\Swagger(
//     *         @SWG\Definition(
//     *             definition="settings",
//     *             @SWG\Property(property="workoutsNumberInPast", type="integer"),
//     *             @SWG\Property(property="workoutsNumberInPlanning", type="integer"),
//     *             @SWG\Property(property="trainingType", type="string"),
//     *             @SWG\Property(property="homeTrainingType", type="string"),
//     *             @SWG\Property(property="trainingIntensity", type="string"),
//     *             @SWG\Property(property="trainingPlan", type="string"),
//     *             @SWG\Property(property="coachingStyle", type="string"),
//     *             @SWG\Property(property="hasPremiumEquipment", type="boolean"),
//     *             @SWG\Property(property="gymEquipment", type="array",
//     *                 @SWG\Items(type="string")
//     *             ),
//     *             @SWG\Property(property="showFullName", type="boolean"),
//     *             @SWG\Property(property="isPrivate", type="boolean"),
//     *             @SWG\Property(property="isBetaClasses", type="boolean"),
//     *             @SWG\Property(property="liveWorkouts", type="boolean"),
//     *             @SWG\Property(property="workoutQuantity", type="integer"),
//     *             @SWG\Property(property="avatarPath", type="string"),
//     *             @SWG\Property(property="avatarUrl", type="string"),
//     *             @SWG\Property(property="isWorkoutLimitDisabled", type="boolean"),
//     *             @SWG\Property(property="isPaymentScreenDisabled", type="boolean"),
//     *             @SWG\Property(property="notifications", type="object",
//     *                 @SWG\Property(property="Team", type="boolean"),
//     *                 @SWG\Property(property="Like", type="boolean"),
//     *                 @SWG\Property(property="Workout Start", type="boolean"),
//     *                 @SWG\Property(property="Milestone", type="boolean"),
//     *                 @SWG\Property(property="Updates", type="boolean"),
//     *                 @SWG\Property(property="Rest Alert", type="boolean"),
//     *             ),
//     *             @SWG\Property(property="widgets", type="array",
//     *                 @SWG\Items(type="string")
//     *             ),
//     *             @SWG\Property(property="gymByDefault", type="object",
//     *                 @SWG\Property(property="name", type="string"),
//     *                 @SWG\Property(property="city", type="string"),
//     *                 @SWG\Property(property="address", type="string"),
//     *                 @SWG\Property(property="coordinate", type="object",
//     *                     @SWG\Property(property="longitude", type="integer"),
//     *                     @SWG\Property(property="latitude", type="integer")
//     *                 )
//     *             ),
//     *             @SWG\Property(property="isAvatarLocked", type="boolean"),
//     *             @SWG\Property(property="isFoundingMember", type="boolean"),
//     *             @SWG\Property(property="isAdmin", type="boolean"),
//     *             @SWG\Property(property="hiddenFromSocial", type="boolean"),
//     *             @SWG\Property(property="isLocked", type="boolean"),
//     *             @SWG\Property(property="showCountDown", type="boolean"),
//     *             @SWG\Property(property="measurementUnits", type="string"),
//     *             @SWG\Property(property="soundAfterRest", type="boolean"),
//     *             @SWG\Property(property="soundAfterTimerEnds", type="boolean"),
//     *             @SWG\Property(property="freeEliteMembership", type="boolean"),
//     *             @SWG\Property(property="showActivitiesInSocial", type="boolean"),
//     *             @SWG\Property(property="displayWeightsOnPublicProfile", type="boolean"),
//     *             @SWG\Property(property="displayWeightsOnPreviewScreen", type="boolean"),
//     *             @SWG\Property(property="wantsWarmups", type="boolean"),
//     *             @SWG\Property(property="gender", type="string"),
//     *             @SWG\Property(property="skillLevel", type="string"),
//     *             @SWG\Property(property="goal", type="string"),
//     *             @SWG\Property(property="trainingComplexity", type="string"),
//     *             @SWG\Property(property="isCreatorStudio", type="boolean"),
//     *             @SWG\Property(property="joined_at", type="integer"),
//     *             @SWG\Property(property="coachPreference", type="string"),
//     *             @SWG\Property(property="language_code", type="string"),
//     *             @SWG\Property(property="language", type="object",
//     *                 @SWG\Property(property="code", type="string"),
//     *                 @SWG\Property(property="name", type="string"),
//     *                 @SWG\Property(property="native_name", type="string"),
//     *                 @SWG\Property(property="is_rtl", type="boolean")
//     *             ),
//     *             @SWG\Property(property="appleHealthConsent", type="integer"),
//     *             @SWG\Property(property="locationConsent", type="integer"),
//     *             @SWG\Property(property="firstWorkoutDate", type="integer"),
//     *             @SWG\Property(property="country", type="object",
//     *                 @SWG\Property(property="name", type="string"),
//     *                 @SWG\Property(property="code", type="string"),
//     *                 @SWG\Property(property="shortCode", type="string")
//     *             ),
//     *             @SWG\Property(property="tagline", type="string"),
//     *             @SWG\Property(property="referrerId", type="string"),
//     *             @SWG\Property(property="instagramId", type="string"),
//     *             @SWG\Property(property="instagramUsername", type="string")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response=200,
//     *         description="Return when success",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="message"),
//     *             @SWG\Property(type="object", property="response",
//     *                 @SWG\Property(property="user_id", type="integer"),
//     *                 @SWG\Property(property="first_name", type="string"),
//     *                 @SWG\Property(property="last_name", type="string"),
//     *                 @SWG\Property(property="email", type="string"),
//     *                 @SWG\Property(property="birthday", type="integer"),
//     *                 @SWG\Property(property="join_date", type="integer"),
//     *                 @SWG\Property(property="workoutQuantity", type="integer"),
//     *                 @SWG\Property(property="partOfWorkoutsWithShreds", type="number"),
//     *                 @SWG\Property(property="followersQuantity", type="integer"),
//     *                 @SWG\Property(property="user_username", type="string"),
//     *                 @SWG\Property(property="settings", ref="#/definitions/settings")
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Get("/me")
//     */
//    public function getUserInformationAction(): JsonResponse
//    {
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => '',
//            'response' => $this->usersService->getUserInformationAction($this->getUser()),
//        ]);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Change user password",
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Get("/")
//     */
//    public function getIndex(): JsonResponse
//    {
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => ''
//        ]);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Change user password",
//     *     @SWG\Parameter(
//     *          name="body",
//     *          in="body",
//     *          required=true,
//     *          @SWG\Schema(
//     *              @SWG\Property(
//     *                  property="current_password",
//     *                  type="string"
//     *              ),
//     *              @SWG\Property(
//     *                  property="new_password",
//     *                  type="string"
//     *              ),
//     *              @SWG\Property(
//     *                  property="repeat_password",
//     *                  type="string"
//     *              )
//     *          )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="203",
//     *         description="Returned when passwords does not match or using old password",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=203),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="401",
//     *         description="Returned when current password is incorrect",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=401),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="500",
//     *         description="Returned when something went wrong",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=500),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Put("/change_password")
//     */
//    public function changePassword(Request $request): JsonResponse
//    {
//        $user = $this->getUser();
//        $parameterBag = $request->request;
//        $response = $this->userHandler->changePassword($parameterBag, $user);
//
//        return JsonResponse::create($response);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"Tracking"},
//     *     summary="Track application start",
//     *     @SWG\Parameter(
//     *          name="body",
//     *          in="body",
//     *          required=true,
//     *          @SWG\Schema(
//     *              @SWG\Property(
//     *                  property="version",
//     *                  type="string"
//     *              )
//     *          )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="boolean", property="should_upgrade")
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/start_app")
//     *
//     * @return mixed[]
//     */
//    public function setDateOfLastAppStart(Request $request): array
//    {
//        $em = $this->getDoctrine()->getManager();
//        $lastVersion = $em->getRepository(MobileVersion::class)->findOneBy([], ['id' => 'desc']);
//        $appVersion = $request->request->get('version', null);
//
//        $shouldUpgrade = false;
//
//        try {
//            $shouldUpgrade = $this->shouldUpgrade($lastVersion, $appVersion);
//        } catch (Exception $e) {
//            //
//        }
//
//        return [
//            'should_upgrade' => $shouldUpgrade
//        ];
//    }
//
//    /**
//     * @Operation(
//     *     tags={"Tracking"},
//     *     summary="Track application start",
//     *     @SWG\Parameter(
//     *          name="body",
//     *          in="body",
//     *          required=true,
//     *          @SWG\Schema(
//     *              @SWG\Property(
//     *                  property="version",
//     *                  type="string",
//     *                  default="Beta"
//     *              ),
//     *              @SWG\Property(
//     *                  property="device_id",
//     *                  type="string",
//     *                  default=""
//     *              )
//     *          )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful"
//     *     ),
//     *     @SWG\Response(
//     *         response="400",
//     *         description="Returned when payload is invalid",
//     *         @SWG\Schema(
//     *             type="object",
//     *             @SWG\Property(property="code", type="number", example=400),
//     *             @SWG\Property(
//     *                 property="errors",
//     *                 type="object",
//     *                 @SWG\Property(property="appVersion", type="array", @SWG\Items(type="string"))
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Put("/version")
//     */
//    public function setVersion(Request $request): JsonResponse
//    {
//        /** @var User $user */
//        $user = $this->getUser();
//
//        $appVersion = $request->request->get('version', 'Beta');
//        $deviceId = $request->request->get('device_id', null);
//
//        $updateAppVersionCommand = new UpdateAppVersionCommand($user, $appVersion);
//        $updateDeviceIdCommand = new UpdateDeviceIdCommand($user, $deviceId);
//        $trackLastStartedApplicationCommand = new TrackLastStartedApplicationCommand($user);
//
//        try {
//            $this->handleCommand($updateAppVersionCommand);
//            $this->handleCommand($updateDeviceIdCommand);
//            $this->handleCommand($trackLastStartedApplicationCommand);
//        } catch (InvalidCommandException $validatorException) {
//            throw new ApiInvalidCommandException($validatorException->getViolations());
//        }
//
//        return JsonResponse::create([]);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Remove account",
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="400",
//     *         description="Returned when payload is invalid",
//     *         @SWG\Schema(
//     *             type="object",
//     *             @SWG\Property(property="code", type="number", example=400),
//     *             @SWG\Property(
//     *                 property="errors",
//     *                 type="object",
//     *                 @SWG\Property(property="deletedUser", type="array", @SWG\Items(type="string"))
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Put("/removeAccount")
//     */
//    public function removeAccount(): JsonResponse
//    {
//        /** @var User $user */
//        $user = $this->getUser();
//
//        try {
//            $this->handleCommand(new SoftDeleteUserCommand($user));
//        } catch (InvalidCommandException $validatorException) {
//            throw new ApiInvalidCommandException($validatorException->getViolations());
//        }
//
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => $this->translator->trans('Account has been deleted'),
//        ]);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="User Data Download",
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="400",
//     *         description="Returned when title or body empty",
//     *         @SWG\Schema(
//     *             type="object",
//     *             @SWG\Property(property="code", type="number", example=400),
//     *             @SWG\Property(property="message", type="string", example="Invalid json payload"),
//     *             @SWG\Property(
//     *                 property="errors",
//     *                 type="object",
//     *                 @SWG\Property(property="user", type="array", @SWG\Items(type="string"))
//     *             )
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/data_download")
//     */
//    public function downloadData(): JsonResponse
//    {
//        /** @var User $user */
//        $user = $this->getUser();
//
//        try {
//            $this->handleCommand(new CreateDataDownloadQueueCommand($user));
//        } catch (InvalidCommandException $validatorException) {
//            throw new ApiInvalidCommandException($validatorException->getViolations());
//        }
//
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => $this->translator->trans('Data Download Queued'),
//        ]);
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Upload user avatar",
//     *     consumes={"multipart/form-data"},
//     *     @SWG\Parameter(
//     *          type="file",
//     *          in="formData",
//     *          required=true,
//     *          name="avatar"
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="message"),
//     *             @SWG\Property(type="string", property="avatarPath"),
//     *             @SWG\Property(type="string", property="avatarUrl")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="400",
//     *         description="Returned when avatar was blocked",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=400),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Post("/upload_avatar")
//     */
//    public function uploadAvatar(Request $request): JsonResponse
//    {
//        $user = $this->getUser();
//
//        if ($user->isAvatarLocked()) {
//            return JsonResponse::create([
//                'code' => JsonResponse::HTTP_BAD_REQUEST,
//                'message' => $this->translator->trans('Changing of avatar was blocked by Admin'),
//            ]);
//        }
//
//        return JsonResponse::create($this->shredUserManager->changeAvatar($request, $user));
//    }
//
//    /**
//     * @Operation(
//     *     tags={"User"},
//     *     summary="Update user information",
//     *     @SWG\Parameter(
//     *          name="body",
//     *          in="body",
//     *          required=true,
//     *          @SWG\Schema(
//     *              @SWG\Property(
//     *                  property="email",
//     *                  type="string",
//     *                  description="User email"
//     *              ),
//     *              @SWG\Property(
//     *                  property="first_name",
//     *                  type="string",
//     *                  description="User first name"
//     *              ),
//     *              @SWG\Property(
//     *                  property="last_name",
//     *                  type="string",
//     *                  description="User last name"
//     *              ),
//     *              @SWG\Property(
//     *                  property="birthday",
//     *                  type="integer",
//     *                  description="User birthday"
//     *              ),
//     *              @SWG\Property(
//     *                  property="user_username",
//     *                  type="string",
//     *                  description="Username"
//     *              ),
//     *              @SWG\Property(
//     *                  property="settings",
//     *                  ref="#/definitions/settings"
//     *              )
//     *          )
//     *     ),
//     *     @SWG\Response(
//     *         response="200",
//     *         description="Returned when successful",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=200),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     ),
//     *     @SWG\Response(
//     *         response="400",
//     *         description="Returned when User Username is busy",
//     *         @SWG\Schema(
//     *             @SWG\Property(type="integer", property="code", example=400),
//     *             @SWG\Property(type="string", property="message")
//     *         )
//     *     )
//     * )
//     *
//     * @Rest\Put("/me")
//     */
//    public function updateUserInformationAction(Request $request): JsonResponse
//    {
//        $this->usersService->updateUserInformationAction($this->getUser(), $request->request);
//
//        return JsonResponse::create([
//            'code' => JsonResponse::HTTP_OK,
//            'message' => $this->translator->trans('User information has updated'),
//        ]);
//    }
//
//    private function shouldUpgrade($version, $currentVersion)
//    {
//        $upgrade = false;
//
//        if (!empty($version) && !empty($currentVersion)) {
//            $upgrade = version_compare($currentVersion, $version->getVersion(), '<');
//        }
//
//        return $upgrade;
//    }
}

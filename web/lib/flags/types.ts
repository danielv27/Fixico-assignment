export type FlagDecisions = Record<string, boolean>;

export type EvaluateRequest = {
  subject: string;
  attributes?: Record<string, string>;
};

export type EvaluateResponse = {
  flags: FlagDecisions;
};
